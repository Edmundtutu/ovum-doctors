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
    public function ussdRequestHandler(Request $request)
    {
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
