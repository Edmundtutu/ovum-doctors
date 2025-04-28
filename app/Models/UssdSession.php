<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UssdSession extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ussd_sessions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session_id',
        'phone_number',
        'service_code',
        'token',
        'data',
        'authenticated',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'authenticated' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Find or create a session by session ID
     *
     * @param string $sessionId
     * @param string $phoneNumber
     * @param string|null $serviceCode
     * @return UssdSession
     */
    public static function findOrCreateSession(string $sessionId, string $phoneNumber, ?string $serviceCode = null): self
    {
        // Look for an existing session
        $session = self::where('session_id', $sessionId)->first();

        // If not found, create a new one
        if (!$session) {
            $session = self::create([
                'session_id' => $sessionId,
                'phone_number' => $phoneNumber,
                'service_code' => $serviceCode,
                'authenticated' => false,
                'expires_at' => Carbon::now()->addHours(1), // Sessions expire after 1 hour
            ]);
        }

        return $session;
    }

    /**
     * Save token to the session and mark as authenticated
     *
     * @param string $token
     * @return $this
     */
    public function saveToken(string $token): self
    {
        $this->token = $token;
        $this->authenticated = true;
        $this->expires_at = Carbon::now()->addHours(1); // Reset expiry
        $this->save();

        return $this;
    }

    /**
     * Clear the authentication token and set as not authenticated
     *
     * @return $this
     */
    public function clearToken(): self
    {
        $this->token = null;
        $this->authenticated = false;
        $this->save();

        return $this;
    }

    /**
     * Check if the session is authenticated
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        // Check if authenticated and not expired
        return $this->authenticated && 
               $this->token && 
               $this->expires_at && 
               $this->expires_at->gt(Carbon::now());
    }

    /**
     * Store arbitrary data in the session
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setData(string $key, $value): self
    {
        $data = $this->data ?: [];
        $data[$key] = $value;
        $this->data = $data;
        $this->save();

        return $this;
    }

    /**
     * Get data from the session
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getData(string $key, $default = null)
    {
        if (!$this->data) {
            return $default;
        }

        return $this->data[$key] ?? $default;
    }
} 