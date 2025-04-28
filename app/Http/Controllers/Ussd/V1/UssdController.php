<?php

namespace App\Http\Controllers\Ussd\v1;

use App\Models\UssdSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Api\Auth\PatientAuthController;
use App\Http\Controllers\Api\CyleHistoryController;

class UssdController extends Controller
{
    use UssdMenuTrait;
    
    protected $apiBaseUrl;
    
    protected $patientAuthController;
    
    protected $cycleHistoryController;
    
    public function __construct()
    {
        // Set the base URL for API calls - adjust this to your actual API URL
        $this->apiBaseUrl = config('app.url') . '/api';
        
        // Resolve the auth controller from the service container
        $this->patientAuthController = app()->make('App\Http\Controllers\Api\Auth\PatientAuthController');
        
        // Resolve the cycle history controller from the service container
        $this->cycleHistoryController = app()->make('App\Http\Controllers\Api\CyleHistoryController');
    }
    
    /**
     * This controller has all the logic of handling a user session on ussd 
     * From Authentication to parting
     */
    public function ussdRequestHandler(Request $request)
    {
        $sessionId = $request['serviceCode']; // for now have been enter changed to keep it simple 
        $serviceCode = $request['sessionId'];
        $phoneNumber = $request['phoneNumber'];
        $text = $request['text'];
        header('Content-type: text/plain');

        // Find or create a session in the database
        $session = UssdSession::findOrCreateSession($sessionId, $phoneNumber, $serviceCode);

        $this->handleMainMenu($text, $phoneNumber, $sessionId);

        exit;
    } 
    
    public function handleMainMenu($text, $phoneNumber, $sessionId)
    {
        // First check if the user is authenticated for this session using the database
        $isAuthenticated = $this->checkIfAuthenticated($sessionId);
        $token = $this->getSessionToken($sessionId);
        
        // Calculate next period date prediction based on user's cycle data
        $nextPeriodDate = $this->calculateNextPeriod($phoneNumber);
        
        // If text is empty, begin with authentication flow
        if (empty($text)) {
            if (!$isAuthenticated) {
                // Not authenticated, ask for passcode
                return $this->ussd_proceed("Welcome to Ovum!\nPlease enter your passcode:");
            } else {
                // Already authenticated, show main menu
                return $this->mainMenu($nextPeriodDate);
            }
        }
        
        // Split the USSD string into an array using "*" as the delimiter
        $ussd_string_exploded = explode("*", $text);
        
        // Get the level of the menu from the USSD string reply
        $level = count($ussd_string_exploded);

        // If not authenticated and not at authentication level, restart flow
        if (!$isAuthenticated && $level > 1) {
            return $this->ussd_stop("Session expired. Please try again.");
        }

        // Declare a An empty logged Symptoms array to store the symptoms that have been looged in the current seesion
        $loggedSymptoms = array();
        
        switch($level) {
            // level 1 of the menu - Authentication or Main Menu options
            case 1:
                if (!$isAuthenticated) {
                    // Process passcode
                    $passcode = trim($ussd_string_exploded[0]);
                    
                    // Validate passcode before attempting login
                    if (empty($passcode)) {
                        return $this->ussd_stop("Passcode cannot be empty. Please try again.");
                    }
                    
                    // Attempt login with the provided passcode
                    try {
                        // Log input values for debugging
                        Log::info('USSD Login attempt:', [
                            'phone' => $phoneNumber, 
                            'device' => 'USSD_'.$sessionId, 
                            'passcode' => $passcode
                        ]);
                        
                        $loginResponse = $this->login($phoneNumber, 'USSD_' . $sessionId, $passcode);
                        
                        // Log response for debugging
                        Log::info('USSD Login response:', $loginResponse);
                        
                        // Check if login was successful
                        if (isset($loginResponse['token'])) {
                            // Store token in database session
                            $this->saveSessionToken($sessionId, $loginResponse['token']);
                            
                            // Show main menu
                            return $this->mainMenu($nextPeriodDate);
                        } else {
                            // Login failed
                            $errorMessage = $loginResponse['message'] ?? 'Invalid passcode';
                            return $this->ussd_stop($errorMessage);
                        }
                    } catch (\Exception $e) {
                        Log::error('USSD Login exception: ' . $e->getMessage());
                        return $this->ussd_stop("Authentication failed: " . $e->getMessage());
                    }
                } else {
                    // User is authenticated, process main menu options
                    switch ($ussd_string_exploded[0]) {
                        case 1: // Log today's activity
                            $this->logActivityMenu();
                            break;
                        case 2: // View cycle history
                            $this->viewHistoryMenu();
                            break;
                        case 3: // Talk to a doctor
                            $this->doctorMenu();
                            break;
                        case 4: // Settings
                            $this->settingsMenu();
                            break;
                        case 0: // Logout
                            $this->clearSession($sessionId);
                            $this->ussd_stop("You have successfully logged out.");
                            break;
                        default:
                            $this->ussd_stop("Invalid option. Please try again.");
                            break;
                    }
                }
                break;
                
            // level 2 of the menu - Submenu options
            case 2:
                switch ($ussd_string_exploded[0]) {
                    case 1: // From Log Activity Menu
                        switch ($ussd_string_exploded[1]) {
                            case 1: // Period started
                                $this->periodFlowMenu();
                                break;
                            case 2: // Spotting
                                $this->spottedMenu();
                                break;
                            case 3: // Vaginal discharge
                                $this->vaginalDischargeMenu();
                                break;
                            case 4: // Digestion/Stool
                                $this->digestionMenu();
                                break;
                            case 5: // Took a pill
                                $this->pillTypeMenu();
                                break;
                            case 6: // Mood/Feeling
                                $this->feelingMenu();
                                break;
                            case 7: // Pain or symptoms
                                $this->painSymptomsMenu(); 
                                break;
                            case 8: // Had sex
                                $this->ussd_proceed("Sex activity logged ✅\n1. Confirm\n0. Cancel");
                                break;
                            case 9: // Nothing happened
                                $this->ussd_stop("No activities logged for today.");
                                break;
                            case 0: // Back to main menu
                                $this->mainMenu($nextPeriodDate);
                                break;
                            default:
                                $this->ussd_stop("Invalid option. Please try again.");
                                break;
                        }
                        break;
                        
                    case 2: // From View History Menu
                        switch ($ussd_string_exploded[1]) {
                            case 1: // Last period date
                                $lastPeriod = $this->getLastPeriodDate($phoneNumber);
                                $this->ussd_stop("Your last period started on: " . $lastPeriod);
                                break;
                            case 2: // Logged symptoms
                                $this->showLoggedSymptoms($phoneNumber);
                                break;
                            case 3: // Past doctor visits
                                $this->showDoctorVisits($phoneNumber);
                                break;
                            case 0: // Back to main menu
                                $this->mainMenu($nextPeriodDate);
                                break;
                            default:
                                $this->ussd_stop("Invalid option. Please try again.");
                                break;
                        }
                        break;
                        
                    case 3: // From Doctor Menu
                        switch ($ussd_string_exploded[1]) {
                            case 1: // Book appointment
                                $this->ussd_proceed("Enter your location to find nearby doctors");
                                break;
                            case 2: // Call doctor
                                $this->ussd_proceed("Enter your doctor's number");
                                break;
                            case 0: // Back to main menu
                                $this->mainMenu($nextPeriodDate);
                                break;
                            default:
                                $this->ussd_stop("Invalid option. Please try again.");
                                break;
                        }
                        break;
                        
                    case 4: // From Settings Menu
                        // Add settings menu handling...
                        $this->ussd_stop("Settings functionality coming soon.");
                        break;
                }
                break;
                
            // level 3 of the menu - Detail selection and confirmation
            case 3:
                switch ($ussd_string_exploded[0]) {
                    case 1: // From Log Activity
                        switch ($ussd_string_exploded[1]) {
                            case 1: // From Period Flow Menu
                                switch ($ussd_string_exploded[2]) {
                                    case 1: // Light
                                    case 2: // Medium
                                    case 3: // Heavy
                                        $flowType = $this->getFlowType($ussd_string_exploded[2]);
                                        $this->ussd_proceed("period $flowType saved \n1. Confirm\n0. Cancel");
                                        break;
                                    case 0: // Back
                                        $this->logActivityMenu();
                                        break;
                                    default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                                break;
                                
                            case 2: // From Spotting Menu
                                switch ($ussd_string_exploded[2]) {
                                    case 1: // Light
                                    case 2: // Medium
                                    case 3: // Heavy
                                        $spottingType = $this->getSpottingType($ussd_string_exploded[2]);
                                        $this->ussd_proceed("spotting: $spottingType saved \n1. Confirm\n0. Cancel ");
                                        break;
                                    case 0: // Back
                                        $this->logActivityMenu();
                                        break;
                                    default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                                break;
                                
                            case 3: // From Vaginal Discharge Menu
                                switch ($ussd_string_exploded[2]) {
                                    case 1: // Clear/Stretchy
                                    case 2: // Creamy
                                    case 3: // Watery
                                    case 4: // Yellow/Green
                                    case 5: // Thick/White
                                        $dischargeType = $this->getDischargeType($ussd_string_exploded[2]);
                                        $this->ussd_proceed("discharge: $dischargeType saved \n1. Confirm\n0. Cancel");
                                        break;
                                    case 0: // Back
                                        $this->logActivityMenu();
                                        break;
                                    default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                                break;
                                
                            case 4: // From Digestion Menu
                                switch ($ussd_string_exploded[2]) {
                                    case 1: // Constipation
                                    case 2: // Diarrhea
                                    case 3: // Normal
                                    case 4: // Bloating
                                        $digestionType = $this->getDigestionType($ussd_string_exploded[2]);
                                        $this->ussd_proceed("digestion: $digestionType saved \n1. Confirm\n0. Cancel");
                                        break;
                                    case 0: // Back
                                        $this->logActivityMenu();
                                        break;
                                    default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                                break;
                                
                            case 5: // From Pill Type Menu
                                switch ($ussd_string_exploded[2]) {
                                    case 1: // Morning-after
                                    case 2: // Birth control
                                    case 3: // Pain relief
                                        $pillType = $this->getPillType($ussd_string_exploded[2]);
                                        $this->ussd_proceed("took pill: $pillType saved \n1. Confirm\n0. Cancel");
                                        break;
                                    case 0: // Back
                                        $this->logActivityMenu();
                                        break;
                                    default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                                break;
                                
                            case 6: // From Feeling Menu
                                switch ($ussd_string_exploded[2]) {
                                    case 1: // Happy
                                    case 2: // Moody
                                    case 3: // Tired
                                    case 4: // Anxious
                                    case 5: // Angry
                                        $feelingType = $this->getFeelingType($ussd_string_exploded[2]);
                                        $this->ussd_proceed("feeling: $feelingType saved \n1. Confirm\n0. Cancel");
                                        break;
                                    case 0: // Back
                                        $this->logActivityMenu();
                                        break;
                                    default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                                break;
                                
                            case 7: // From Pain/Symptoms Menu
                                switch ($ussd_string_exploded[2]) {
                                    case 1: // Cramps
                                    case 2: // Tender breasts
                                    case 3: // Headache
                                    case 4: // Abdominal pain
                                    case 5: // None
                                        $symptomType = $this->getSymptomType($ussd_string_exploded[2]);
                                        $this->ussd_proceed("Pain symptom: $symptomType saved \n1. Confirm\n0. Cancel");
                                        break;
                                    case 0: // Back
                                        $this->logActivityMenu();
                                        break;
                                    default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                                break;
                            case 8: // Had sex (confirm or back)
                                switch ($ussd_string_exploded[2]) {
                                    case 1: // Confirm
                                        $loggedSymptoms[] = 'Sexual activity';
                                        $token = $this->getSessionToken($sessionId);
                                        $this->saveSymptoms($token, $loggedSymptoms);
                                        $this->ussd_stop("Successfully logged sexual activity");
                                        break;
                                    case 0: // Back to previous menu
                                        $this->logActivityMenu();
                                        break;
                                    default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                                break;
                        }
                        break;
                        
                    case 3: // From Doctor Menu - handle location or doctor number entry
                        if ($ussd_string_exploded[1] == 1) {
                            // Process location for nearby doctors
                            $location = $ussd_string_exploded[2];
                            $doctors = $this->findNearbyDoctors($location);
                            $this->ussd_proceed("Doctors near $location:\n" . $doctors);
                        } else if ($ussd_string_exploded[1] == 2) {
                            // Process doctor's number
                            $doctorNumber = $ussd_string_exploded[2];
                            $this->ussd_stop("We'll connect you with doctor at $doctorNumber shortly.");
                        }
                        break;
                }
                break;
                
            // level 4 of the menu - PIN confirmation and saving data
            case 4:                
                switch ($ussd_string_exploded[0]) {
                    case 1: // From Log Activity
                        switch ($ussd_string_exploded[1]) {
                            case 1: // Period Flow
                                switch ($ussd_string_exploded[3]){
                                    case 1: // Confirm
                                        $flowType = $this->getFlowType($ussd_string_exploded[2]);
                                        //  for cases of Period Logging, call both functions for saving the period symptoms
                                        //  and period start (for cycle changes). 
                                        $loggedSymptoms[] = $flowType.' Period';
                                        $token = $this->getSessionToken($sessionId);
                                        
                                        // First log the period start in the API
                                        $startResponse = $this->savePeriodStart($token);
                                        
                                        // Then save the symptoms
                                        $symptomResponse = $this->saveSymptoms($token, $loggedSymptoms);
                                        
                                        $this->ussd_stop("Successfully logged period flow: $flowType");
                                        break;

                                    case 0: // Back to previous menu
                                        $this->periodFlowMenu();
                                        break;
                                        default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                                break;
                                
                            case 2: // Spotting
                                switch ($ussd_string_exploded[3]){
                                    case 1: // Confirm
                                        $spottingType = $this->getSpottingType($ussd_string_exploded[2]);
                                        $loggedSymptoms[] = $spottingType.' Spotting';
                                        $token = $this->getSessionToken($sessionId);
                                        $this->saveSymptoms($token, $loggedSymptoms);
                                        $this->ussd_stop("Successfully logged spotting: $spottingType");
                                        break;
                                    case 0: // Back to previous menu
                                        $this->spottedMenu();
                                        break;
                                        default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                              break;  
                                
                            case 3: // Vaginal Discharge
                                switch ($ussd_string_exploded[3]){
                                    case 1: // Confirm
                                        $dischargeType = $this->getDischargeType($ussd_string_exploded[2]);
                                        $loggedSymptoms[] = $dischargeType.' Discharge';
                                        $token = $this->getSessionToken($sessionId);
                                        $this->saveSymptoms($token, $loggedSymptoms);
                                        $this->ussd_stop("Successfully logged discharge: $dischargeType");
                                        break;
                                    case 0: // Back to previous menu
                                        $this->vaginalDischargeMenu();
                                        break;
                                        default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                                break; 
                                
                            case 4: // Digestion
                                switch ($ussd_string_exploded[3]){
                                    case 1: // Confirm
                                        $digestionType = $this->getDigestionType($ussd_string_exploded[2]);
                                        $loggedSymptoms[] = $digestionType.' Digestion';
                                        $token = $this->getSessionToken($sessionId);
                                        $this->saveSymptoms($token, $loggedSymptoms);
                                        $this->ussd_stop("Successfully logged digestion: $digestionType");
                                        break;
                                    case 0: // Back to previous menu
                                        $this->digestionMenu();
                                        break;
                                        default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                        break;
                                }
                                break;
                                    
                                
                            case 5: // Pill
                                switch ($ussd_string_exploded[3]){
                                    case 1: // Confirm
                                        $pillType = $this->getPillType($ussd_string_exploded[2]);
                                        $loggedSymptoms[] = $pillType.' Pill';
                                        $token = $this->getSessionToken($sessionId);
                                        $this->saveSymptoms($token, $loggedSymptoms);
                                        $this->ussd_stop("Successfully logged pill: $pillType");
                                        break;
                                    case 0: // Back to previous menu
                                        $this->pillTypeMenu();
                                        break;
                                        default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                            break;
                                    }
                                break;
                                 
                                
                            case 6: // Feeling
                                switch ($ussd_string_exploded[3]){
                                    case 1: // Confirm
                                        $feelingType = $this->getFeelingType($ussd_string_exploded[2]);
                                        $loggedSymptoms[] = $feelingType.' Mood';
                                        $token = $this->getSessionToken($sessionId);
                                        $this->saveSymptoms($token, $loggedSymptoms);
                                        $this->ussd_stop("Successfully logged feeling: $feelingType");
                                        break;
                                    case 0: // Back to previous menu
                                        $this->feelingMenu();
                                        break;
                                        default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                                break;
                                }
                                break;
                                
                            case 7: // Pain/Symptoms
                                switch ($ussd_string_exploded[3]){
                                    case 1: // Confirm
                                        $symptomType = $this->getSymptomType($ussd_string_exploded[2]);
                                        $loggedSymptoms[] = $symptomType.' Pain';
                                        $token = $this->getSessionToken($sessionId);
                                        $this->saveSymptoms($token, $loggedSymptoms);
                                        $this->ussd_stop("Successfully logged symptom: $symptomType");
                                        break;
                                    case 0: // Back to previous menu
                                        $this->painSymptomsMenu();
                                        break;
                                        default:
                                        $this->ussd_stop("Invalid option. Please try again.");
                                                    break;
                                }
                                break;
                        }
                        break;
                }
                break;
                
            default:
                $this->ussd_stop("Session expired. Please try again.");
                break;
        }
    }
    
    // Standard USSD response for continuing session
    private function ussd_proceed($message)
    {
        echo "CON " . $message;
        exit;
    }
    
    // Standard USSD stop response
    private function ussd_stop($message)
    {
        echo "END " . $message;
        exit;
    }
    
    // Session Management Methods - Updated to use database instead of static arrays
    
    /**
     * Check if user is authenticated in this session
     * 
     * @param string $sessionId The USSD session ID
     * @return bool
     */
    private function checkIfAuthenticated($sessionId)
    {
        $session = UssdSession::where('session_id', $sessionId)->first();
        
        if (!$session) {
            return false;
        }
        
        return $session->isAuthenticated();
    }
    
    /**
     * Save token to database session
     * 
     * @param string $sessionId The USSD session ID
     * @param string $token The auth token
     */
    private function saveSessionToken($sessionId, $token)
    {
        $session = UssdSession::where('session_id', $sessionId)->first();
        
        if ($session) {
            $session->saveToken($token);
        }
    }
    
    /**
     * Get token from database session
     * 
     * @param string $sessionId The USSD session ID
     * @return string|null The token or null if not found
     */
    private function getSessionToken($sessionId)
    {
        $session = UssdSession::where('session_id', $sessionId)->first();
        
        if (!$session || !$session->isAuthenticated()) {
            return null;
        }
        
        return $session->token;
    }
    
    /**
     * Clear session data (for logout)
     * 
     * @param string $sessionId The USSD session ID
     */
    private function clearSession($sessionId)
    {
        $session = UssdSession::where('session_id', $sessionId)->first();
        
        if ($session) {
            $session->clearToken();
        }
    }
    
    // Helper methods for getting text values based on numeric inputs
    
    private function getFlowType($value)
    {
        $types = [1 => "Light", 2 => "Medium", 3 => "Heavy"];
        return $types[$value] ?? "Unknown";
    }
    
    private function getSpottingType($value)
    {
        $types = [1 => "Light", 2 => "Medium", 3 => "Heavy"];
        return $types[$value] ?? "Unknown";
    }
    
    private function getDischargeType($value)
    {
        $types = [
            1 => "Clear/Stretchy (fertile)", 
            2 => "Creamy", 
            3 => "Watery", 
            4 => "Yellow/Green (possible infection)",
            5 => "Thick/White"
        ];
        return $types[$value] ?? "Unknown";
    }
    
    private function getDigestionType($value)
    {
        $types = [1 => "Constipation", 2 => "Diarrhea", 3 => "Normal", 4 => "Bloating"];
        return $types[$value] ?? "Unknown";
    }
    
    private function getPillType($value)
    {
        $types = [1 => "Morning-after", 2 => "Birth control", 3 => "Pain relief"];
        return $types[$value] ?? "Unknown";
    }
    
    private function getFeelingType($value)
    {
        $types = [1 => "Happy", 2 => "Moody", 3 => "Tired", 4 => "Anxious", 5 => "Angry"];
        return $types[$value] ?? "Unknown";
    }
    
    private function getSymptomType($value)
    {
        $types = [
            1 => "Cramps", 
            2 => "Tender breasts", 
            3 => "Headache", 
            4 => "Abdominal pain",
            5 => "None"
        ];
        return $types[$value] ?? "Unknown";
    }
    
    private function calculateNextPeriod($phoneNumber)
    {
        // We should get this from the API in a real implementation
        // For now, just return a date 28 days from now
        $today = Carbon::now();
        $nextPeriod = $today->addDays(28)->format('Y-m-d');
        return $nextPeriod;
    }
    
    private function getLastPeriodDate($phoneNumber)
    {
        // In a real implementation, we'd query the API for this
        return "2023-04-15";
    }
    
    private function showLoggedSymptoms($phoneNumber)
    {
        // In a real implementation, we'd query the API for this
        $recentSymptoms = "Recent symptoms:\n- Cramps (2023-04-15)\n- Headache (2023-04-16)";
        $this->ussd_stop($recentSymptoms);
    }
    
    private function showDoctorVisits($phoneNumber)
    {
        $doctorVisits = "Recent doctor visits:\nNone recorded";
        $this->ussd_stop($doctorVisits);
    }
    
    /**
     * Function to handle authentication of the GSM user
     * 
     * Creates a subrequest to The api route /api/patient/login
     * @param string $phone The phone number
     * @param string $devicename Device identifier
     * @param string $passcode User's passcode
     * @return array Authentication response
     */
    protected function login(string $phone, string $devicename, string $passcode)
    {
        // Debug: Check if values are empty
        if (empty($passcode)) {
            return ['message' => 'Passcode cannot be empty', 'status' => 422];
        }

        // Create parameters array - ensure we use the exact parameter names expected by the API
        $params = [
            'phoneNumber' => $phone,  // This is the primary key expected by the API
            'device_name' => $devicename, // This is the exact key expected by the API
            'passcode' => $passcode,
        ];

        // Log the exact parameters we're sending
        Log::info('USSD Login request parameters:', $params);

        try {
            // Create a proper request instance with the parameters
            $request = new Request($params);
            
            // Call the controller method directly
            $response = $this->patientAuthController->login($request);
            
            // Convert the response to an array
            $status = $response->getStatusCode();
            $data = json_decode($response->getContent(), true);
            
            // Debug: If response indicates validation errors, include them in the message
            if ($status === 422 && isset($data['errors'])) {
                $errorMsg = "Validation failed: ";
                foreach ($data['errors'] as $field => $errors) {
                    $errorMsg .= "$field (" . implode(', ', $errors) . "). ";
                }
                return ['message' => $errorMsg, 'status' => $status];
            }

            if ($status === 200 && isset($data['token'])) {
                return $data;
            }

            return ['message' => $data['message'] ?? 'Login failed', 'status' => $status];
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return ['message' => 'An error occurred during login. Please try again.', 'status' => 500];
        }
    }

    // testing function for logging in request
    protected function loginRequest(Request $request)
    {
        $phone = $request['phoneNumber'] ?? $request['phone'] ?? null;
        $devicename = $request['device_name'] ?? $request['devicename'] ?? 'web';
        $passcode = $request['passcode'] ?? null;
        
        // Validate required fields
        if (empty($phone)) {
            return response()->json(['message' => 'Phone number is required'], 422);
        }
        
        if (empty($passcode)) {
            return response()->json(['message' => 'Passcode is required'], 422);
        }
        
        $response = $this->login($phone, $devicename, $passcode);
        if (isset($response['token'])) {
            return response()->json([
                'message' => 'Login successful',
                'token' => $response['token'],
                'patient' => $response['patient']
            ]);
        } else {
            return response()->json(['message' => $response['message'],'status'=> $response['status']], 401);
        }
    }
    

    /**
     * Save symptoms to the user's cycle history
     * 
     * @param string $token Authentication token
     * @param array $symptoms Array of symptoms to log
     * @return mixed API response
     */
    protected function saveSymptoms(string $token, array $symptoms)
    {
        try {
            // First, we need to get the current month in the required format
            $currentMonth = Carbon::now()->format('Y-m');
            
            // Create a request with auth token
            $request = new Request([
                'month' => $currentMonth,
                'symptoms' => $symptoms // Already formatted as an array of strings
            ]);
            
            // Set the authenticated user
            $patient = $this->getPatientFromToken($token);
            if (!$patient) {
                return null;
            }
            
            $request->setUserResolver(function () use ($patient) {
                return $patient;
            });
            
            // Call the controller method directly
            $response = $this->cycleHistoryController->store($request);
            
            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                return json_decode($response->getContent(), true);
            } else {
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Save symptoms error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Record the start of a period in the cycle history
     * 
     * @param string $token Authentication token
     * @param string|null $startDate Optional custom start date
     * @return mixed API response
     */
    protected function savePeriodStart(string $token, ?string $startDate = null)
    {
        try {
            $data = [
                'period_start_date' => $startDate ?? Carbon::now()->format('Y-m-d'),
                'month' => Carbon::now()->format('Y-m')
            ];
            
            // Create a request with auth token
            $request = new Request($data);
            
            // Set the authenticated user
            $patient = $this->getPatientFromToken($token);
            if (!$patient) {
                return null;
            }
            
            $request->setUserResolver(function () use ($patient) {
                return $patient;
            });
            
            // Call the controller method directly
            $response = $this->cycleHistoryController->startPeriod($request);
            
            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                return json_decode($response->getContent(), true);
            } else {
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Save period start error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Record the end of a period in the cycle history
     * 
     * @param string $token Authentication token
     * @param string|null $endDate Optional custom end date
     * @param int|null $newCycleLength Optional next cycle length
     * @param int|null $newPeriodLength Optional next period length
     * @return mixed API response
     */
    protected function savePeriodEnd(
        string $token, 
        ?string $endDate = null,
        ?int $newCycleLength = null,
        ?int $newPeriodLength = null
    ) {
        try {
            $data = [];
            if ($endDate) {
                $data['period_end_date'] = $endDate;
            } else {
                $data['period_end_date'] = Carbon::now()->format('Y-m-d');
            }
            
            if ($newCycleLength) {
                $data['cycle_length'] = $newCycleLength;
            }
            
            if ($newPeriodLength) {
                $data['period_length'] = $newPeriodLength;
            }
            
            $data['month'] = Carbon::now()->format('Y-m');
            
            // Create a request with auth token
            $request = new Request($data);
            
            // Set the authenticated user
            $patient = $this->getPatientFromToken($token);
            if (!$patient) {
                return null;
            }
            
            $request->setUserResolver(function () use ($patient) {
                return $patient;
            });
            
            // Call the controller method directly
            $response = $this->cycleHistoryController->endPeriod($request);
            
            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                return json_decode($response->getContent(), true);
            } else {
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Save period end error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get cycle history for the authenticated user
     * 
     * @param string $token Authentication token
     * @return mixed API response with cycle history data
     */
    protected function getCycleHistory(string $token)
    {
        try {
            // Create an empty request
            $request = new Request();
            
            // Set the authenticated user
            $patient = $this->getPatientFromToken($token);
            if (!$patient) {
                return [];
            }
            
            $request->setUserResolver(function () use ($patient) {
                return $patient;
            });
            
            // Call the controller method directly
            $response = $this->cycleHistoryController->index($request);
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getContent(), true);
                return $data['data'] ?? [];
            } else {
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Get cycle history error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Find nearby doctors based on location
     * 
     * @param string $location User's location
     * @return string Formatted list of doctors
     */
    protected function findNearbyDoctors($location)
    {
        // This would connect to a real API in production
        // For now, return sample data
        return "1. Dr. Brenda (2km)\n2. Dr. Mathias (3km)\n3. City Hospital (5km)";
    }

    /**
     * Helper to get patient from token
     */
    private function getPatientFromToken(string $token)
    {
        try {
            // Get patient from token
            // This will need to be adjusted based on your auth method
            $tokenParts = explode('|', $token);
            $tokenId = $tokenParts[0];
            
            $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if (!$personalAccessToken) {
                return null;
            }
            
            return $personalAccessToken->tokenable;
        } catch (\Exception $e) {
            Log::error('Error getting patient from token: ' . $e->getMessage());
            return null;
        }
    }
}
