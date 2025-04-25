<?php

namespace App\Http\Controllers\Ussd\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class UssdController extends Controller
{
    /**
     * This controller has all th elogic of handling a user seesion on ussd 
     * From Authentication to parting
    */
    public function ussdRequestHandler(Request $request){
        $sessionId = $request['sessionId'];
        $serviceCode = $request['serviceCode'];
        $phoneNumber = $request['phoneNumber'];
        $text = $request['text'];
        header('Content-type: text/plain');
        
        // Handle the USSD request here
        // You can use the sessionId, serviceCode, phoneNumber, and text to process the request

        // Example response
        return response()->json([
            'sessionId' => $sessionId,
            'serviceCode' => $serviceCode,
            'phoneNumber' => $phoneNumber,
            'text' => $text,
            'response' => 'Your USSD response goes here'
        ]);
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
     * Function to save a cycle-history of the ussd session
     * 
     * Makes a post request to the api route /api/cycle-history with Auth token in authorization header
     * @param string $token, $month, $cycleLength, $periodLenth, $periodStartDate, 
     * $periodEndDate, $cycleStartDate, $cycleEndDate, array $symptoms
     * @return $response : response from the api 
     */
    protected function saveCycleHistory(
        string $token,
        string $month,
        int $cycleLength,
        int $periodLength,
        string $periodStartDate,
        string $periodEndDate,
        string $cycleStartDate,
        string $cycleEndDate,
        array $symptoms
    ) {
        try {
            return Http::withToken($token)->post('/api/cycle-history', [
            'month' => $month,
            'cycle_length' => $cycleLength,
            'period_length' => $periodLength,
            'period_start_date' => $periodStartDate,
            'period_end_date' => $periodEndDate,
            'cycle_start_date' => $cycleStartDate,
            'cycle_end_date' => $cycleEndDate,
            'symptoms' => json_encode($symptoms)
            ]);
        } catch (\Exception $e) {
            return response()->json([
            'message' => 'Failed to save cycle history: ' . $e->getMessage()
            ], 500);
        }
    }

    protected  function saveSymptoms(
        string $token,
        array $symptoms
    ) {
        try {
            return Http::withToken($token)->post('/api/cycle-history/symptoms', [
                'symptoms' => json_encode($symptoms)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save symptoms: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function saveNewCycle(
        string $token,
        string $month,
        int $cycleLength,
        int $periodLength,
        string $cycleStartDate,
        string $cycleEndDate
    ) {
        try {
            return Http::withToken($token)->post('/api/cycle-history/new-cycle', [
                'month' => $month,
                'cycle_length' => $cycleLength,
                'period_length' => $periodLength,
                'cycle_start_date' => $cycleStartDate,
                'cycle_end_date' => $cycleEndDate
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save new cycle: ' . $e->getMessage()
            ], 500);
        }
    }
}
