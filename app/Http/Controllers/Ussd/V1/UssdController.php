<?php

namespace App\Http\Controllers\Ussd\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class UssdController extends Controller
{
    use UssdMenuTrait;
    /**
     * This controller has all th elogic of handling a user seesion on ussd 
     * From Authentication to parting
     */
    public function ussdRequestHandler(Request $request)
    {
        $sessionId = $request['sessionId'];
        $serviceCode = $request['serviceCode'];
        $phoneNumber = $request['phoneNumber'];
        $text = $request['text'];
        header('Content-type: text/plain');


        $this->handleMainMenu($text, $phoneNumber);

        exit;
        // Handle the USSD request here
        // You can use the sessionId, serviceCode, phoneNumber, and text to process the request

    } 
    public function handleMainMenu($text, $phoneNumber)
    {
        
        // Calculate next period date prediction based on user's cycle data
        $nextPeriodDate = $this->calculateNextPeriod($phoneNumber);
        
        // If text is empty, show the main menu
        if (empty($text)) {
            return $this->mainMenu($nextPeriodDate);
        }
        
        // Split the USSD string into an array using "*" as the delimiter
        $ussd_string_exploded = explode("*", $text);
        
        // Get the level of the menu from the USSD string reply
        $level = count($ussd_string_exploded);

        // Declare a An empty logged Symptoms array to store the symptoms that have been looged in the current seesion
        $loggedSymptoms= array();
        
        switch($level) {
            // level 1 of the menu - Main Menu options
            case 1:
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
                        $this->ussd_stop("You have successfully logged out.");
                        break;
                    default:
                        $this->ussd_stop("Invalid option. Please try again.");
                        break;
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
                                $lastPeriod = $this->getLastPeriodDate($user);
                                $this->ussd_stop("Your last period started on: " . $lastPeriod);
                                break;
                            case 2: // Logged symptoms
                                $this->showLoggedSymptoms($user);
                                break;
                            case 3: // Past doctor visits
                                $this->showDoctorVisits($user);
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
                                            $this->saveSymptom($user, Carbon::now(), "Sex", 1);
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
                                        //  for cases of Period Logging, call bith functions for saving the period symptoms
                                        //  and period start (for cycle changes). 
                                        $loggedSymptoms [] = $flowType.'Period';
                                        $this->savePeriodStarted($token);
                                        $this->saveSymptoms($token, $loggedSymptoms);
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
                                        $loggedSymptoms [] = $spottingType.'spotting';
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
                                        $loggedSymptoms [] = $dischargeType.'discharge';
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
                                        $loggedSymptoms [] = $digestionType.'digestionType';
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
                                        $loggedSymptoms [] =$pillType .'pillType';
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
                                        $loggedSymptoms [] =$feelingType .'feelingType';
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
                                   
                                
                            case 7: // Pain/Symptoms
                                switch ($ussd_string_exploded[3]){
                                    case 1: // Confirm
                                        $symptomType = $this->getSymptomType($ussd_string_exploded[2]);
                                        $loggedSymptoms [] =$symptomType .'$symptomType';
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
                                    
                                    
                        }
                        break;
                }
                break;
                
            default:
                $this->ussd_stop("Session expired. Please try again.");
                break;
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
    
    
    private function calculateNextPeriod($user)
    {
        // Calculate next period date based on user's cycle history
        // Return formatted date string
            $nextPeriod = '2025-05-01'; // Replace with real data
        return $nextPeriod;
    }
    
    // private function getLastPeriodDate($user)
    // {
    //     // Get user's last recorded period from database
    //     // Return formatted date string
    // }
    
    // private function showLoggedSymptoms($user)
    // {
    //     // Get recent symptoms from database and format for USSD display
    //     // Example implementation
    //     $recentSymptoms = "Recent symptoms:\n";
    //     // Add logic to fetch and format symptoms
    //     $this->ussd_stop($recentSymptoms);
    // }
    
    // private function showDoctorVisits($user)
    // {
    //     // Get doctor visits from database and format for USSD display
    //     // Example implementation
    //     $doctorVisits = "Recent doctor visits:\n";
    //     // Add logic to fetch and format visits
    //     $this->ussd_stop($doctorVisits);
    // }
    
    

    // Standard USSD stop response
    private function ussd_stop($message)
    {
        echo "END " . $message;
        exit;
    }


    /**
     * Function to handle authentication of the GSM user
     * 
     * Sends a Post request to The api route /api/patient/login
     * @param mixed $phone, $devicename, $passcode are required in the Request body
     * @return $token : bearer token for the logged in user
     */
    public function login(string $phone, string $devicename, string $passcode)
    {
        return Http::async()->post('/api/patient/login', [
            'phone' => $phone,
            'devicename' => $devicename,
            'passcode' => $passcode
        ])->then(
            function ($response) {
                $token = $response->json();
                if ($token) {
                    return [
                        'token' => $token,
                        'patient' => $response->json('patient'),
                        'message' => 'Login successful'
                    ];
                }
                throw new \Exception('Invalid credentials', 401);
            },
            function ($exception) {
                return response()->json([
                    'message' => 'Login failed: ' . $exception->getMessage()
                ], $exception->getCode() ?: 500);
            }
        );
    }

    /**
     * Fucntion to get the cycle history of a patient for past 3 months
    */
    protected function getCycleHistory(){
        $currentMonth = now()->format('Y-m-d');
        $pastThreeMonths = now()->subMonths(3)->format('Y-m-d');
        try {
            return Http::get('api/cycle-histories', [
                'month' => "{$currentMonth}-{$pastThreeMonths}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch cycle history: ' . $e->getMessage()
            ], 500);
        }
    }

    protected  function saveSymptoms(
        string $token,
        array $symptoms
    ) {
        try {
            return Http::withToken($token)->post('/api/cycle-history', [
                'symptoms' => json_encode($symptoms)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save symptoms: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function savePeriodStart(
        string $token,
        ?string $month, // month of recording the period start 
        ?string $periodStartDate, // may be not be null if the period did not occur in the current month 
    ) {
        try {
            return Http::withToken($token)->post('/api/cycle-history/start-period', [
                'month' => $month,
                'period_start_date' => $periodStartDate,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save new cycle: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function savePeriodEnd(){
        try {
            return Http::post('/api/cycle-history/end-period');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save period end: ' . $e->getMessage()
            ], 500);
        }        
    }
}
