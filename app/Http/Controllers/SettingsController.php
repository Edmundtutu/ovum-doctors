<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     */
    public function index(): View
    {
        $doctor = auth()->user();
        $clinic = $doctor->clinic;

        return view('settings.index', compact('doctor', 'clinic'));
    }

    /**
     * Update doctor profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $doctor = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('doctors')->ignore($doctor->id)],
            'phone' => 'required|string|max:20',
            'specialization' => 'required|string|max:100',
            'license_number' => ['required', 'string', 'max:50', Rule::unique('doctors')->ignore($doctor->id)],
            'avatar' => 'nullable|image|max:2048'
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($doctor->avatar) {
                Storage::delete('public/avatars/' . $doctor->avatar);
            }

            $avatarPath = $request->file('avatar')->store('public/avatars');
            $validated['avatar'] = basename($avatarPath);
        }

        $doctor->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update clinic settings.
     */
    public function updateClinic(Request $request): RedirectResponse
    {
        $clinic = auth()->user()->clinic;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('clinics')->ignore($clinic->id)],
            'address' => 'required|string|max:500',
            'contact_number' => 'required|string|max:20',
            'email' => ['required', 'email', 'max:255', Rule::unique('clinics')->ignore($clinic->id)],
            'license_number' => ['required', 'string', 'max:50', Rule::unique('clinics')->ignore($clinic->id)],
            'logo' => 'nullable|image|max:2048'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($clinic->logo) {
                Storage::delete('public/logos/' . $clinic->logo);
            }

            $logoPath = $request->file('logo')->store('public/logos');
            $validated['logo'] = basename($logoPath);
        }

        $clinic->update($validated);

        return back()->with('success', 'Clinic settings updated successfully.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed|different:current_password'
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'appointment_reminders' => 'boolean',
            'reminder_time' => 'required_if:appointment_reminders,true|nullable|integer|min:1|max:72'
        ]);

        auth()->user()->update([
            'notification_preferences' => $validated
        ]);

        return back()->with('success', 'Notification preferences updated successfully.');
    }

    /**
     * Update working hours.
     */
    public function updateWorkingHours(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'working_hours' => 'required|array',
            'working_hours.*.day' => 'required|integer|between:0,6',
            'working_hours.*.start' => 'required|date_format:H:i',
            'working_hours.*.end' => 'required|date_format:H:i|after:working_hours.*.start',
            'working_hours.*.is_working' => 'required|boolean'
        ]);

        auth()->user()->update([
            'working_hours' => $validated['working_hours']
        ]);

        return back()->with('success', 'Working hours updated successfully.');
    }
} 