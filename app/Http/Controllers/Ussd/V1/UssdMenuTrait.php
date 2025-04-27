<?php

namespace App\Http\Controllers\Ussd\V1;

trait UssdMenuTrait
{
    // Home menu for registered users
    public function mainMenu($nextPeriodDate)
    {
        $menu  = "Welcome to Ovum\n";
        $menu .= "Your personal period & fertility tracker.\n";
        $menu  .= "Next Period: $nextPeriodDate\n";
        $menu .= "1. Log today's activity\n";
        $menu .= "2. View cycle history\n";
        $menu .= "3. Talk to a doctor\n";
        $menu .= "0. Logout";
        $this->ussd_proceed($menu);
    }

    // Log daily activity
    public function logActivityMenu()
    {
        $menu  = "What happened today?\n";
        $menu .= "1. Period started\n";
        $menu .= "2. Spotting\n";
        $menu .= "3. Vaginal discharge\n";
        $menu .= "4. Digestion/Stool\n";
        $menu .= "5. Took a pill\n";
        $menu .= "6. Mood/Feeling\n";
        $menu .= "7. Pain or symptoms\n";
        $menu .= "8. Had sex\n";
        $menu .= "9. Nothing happened\n";
        $menu .= "0. Back";
        $this->ussd_proceed($menu);
    }

    public function periodFlowMenu()
    {
        $menu  = "Period flow level:\n";
        $menu .= "1. Light\n";
        $menu .= "2. Medium\n";
        $menu .= "3. Heavy\n";
        $menu .= "0. Back";
        $this->ussd_proceed($menu);
    }

    public function spottedMenu()
    {
        $menu  = "Spotting level:\n";
        $menu .= "1. Light\n";
        $menu .= "2. Medium\n";
        $menu .= "3. Heavy\n";
        $menu .= "0. Back";
        $this->ussd_proceed($menu);
    }

    public function vaginalDischargeMenu()
    {
        $menu  = "Discharge type:\n";
        $menu .= "1. Clear/Stretchy (fertile)\n";
        $menu .= "2. Creamy\n";
        $menu .= "3. Watery\n";
        $menu .= "4. Yellow/Green (possible infection)\n";
        $menu .= "5. Thick/White\n";
        $menu .= "0. Back";
        $this->ussd_proceed($menu);
    }

    public function digestionMenu()
    {
        $menu  = "Digestion/Stool today:\n";
        $menu .= "1. Constipation\n";
        $menu .= "2. Diarrhea\n";
        $menu .= "3. Normal\n";
        $menu .= "4. Bloating\n";
        $menu .= "0. Back";
        $this->ussd_proceed($menu);
    }

    public function pillTypeMenu()
    {
        $menu  = "Which pill did you take?\n";
        $menu .= "1. Morning-after\n";
        $menu .= "2. Birth control\n";
        $menu .= "3. Pain relief\n";
        $menu .= "0. Back";
        $this->ussd_proceed($menu);
    }

    public function feelingMenu()
    {
        $menu  = "How are you feeling today?\n";
        $menu .= "1. Happy\n";
        $menu .= "2. Moody\n";
        $menu .= "3. Tired\n";
        $menu .= "4. Anxious\n";
        $menu .= "5. Angry\n";
        $menu .= "0. Back";
        $this->ussd_proceed($menu);
    }

    public function painSymptomsMenu()
    {
        $menu  = "Pain/Symptoms today:\n";
        $menu .= "1. Cramps\n";
        $menu .= "2. Tender breasts\n";
        $menu .= "3. Headache\n";
        $menu .= "4. Abdominal pain\n";
        $menu .= "5. None of these\n";
        $menu .= "0. Back";
        $this->ussd_proceed($menu);
    }

    public function viewHistoryMenu()
    {
        $menu  = "Cycle History:\n";
        $menu .= "1. Last period date\n";
        $menu .= "2. Logged symptoms\n";
        $menu .= "3. Past doctor visits\n";
        $menu .= "0. Back";
        $this->ussd_proceed($menu);
    }

    public function doctorMenu()
    {
        $menu  = "Talk to a doctor:\n";
        $menu .= "1. Book appointment\n";
        $menu .= "2. Call doctor\n";
        $menu .= "0. Back";
        $this->ussd_proceed($menu);
    }

    // Standard USSD response
    private function ussd_proceed($message)
    {
        echo "CON " . $message;
        exit;
    }
}
