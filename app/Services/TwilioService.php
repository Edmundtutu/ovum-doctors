<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected Client $client;
    protected string $verifyServiceSid;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $this->verifyServiceSid = config('services.twilio.verify_service_sid');
    }

    /**
     * Send an OTP to the given phone number using Twilio Verify.
     *
     * @param string $phoneNumber The phone number in E.164 format (e.g., +256763977921)
     * @return \Twilio\Rest\Verify\V2\Service\VerificationInstance
     */
    public function sendOTP(string $phoneNumber)
    {
        return $this->client->verify->v2->services($this->verifyServiceSid)
            ->verifications
            ->create($phoneNumber, 'sms');
    }

    /**
     * Verify the OTP provided by the user.
     *
     * @param string $phoneNumber The phone number in E.164 format.
     * @param string $code The OTP code provided by the user.
     * @return \Twilio\Rest\Verify\V2\Service\VerificationCheckInstance
     */
    public function verifyOTP(string $phoneNumber, string $code)
    {
        return $this->client->verify->v2->services($this->verifyServiceSid)
            ->verificationChecks
            ->create([
                'to'   => $phoneNumber,
                'code' => $code,
            ]);
    }
}
