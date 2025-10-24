<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SchoolHoursRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            // Convert 12-hour format to 24-hour format for comparison
            $timeFormat = 'g:i A'; // Default format
            $time = \Carbon\Carbon::createFromFormat($timeFormat, $value);
            
            // School hours: 7:00 AM to 9:00 PM (19:00 to 21:00 in 24-hour format)
            $schoolStart = \Carbon\Carbon::createFromTime(7, 0, 0);
            $schoolEnd = \Carbon\Carbon::createFromTime(21, 0, 0);
            
            return $time->between($schoolStart, $schoolEnd);
        } catch (\Exception $e) {
            // If time format is invalid, let the date_format validation rule handle it
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be between 7:00 AM and 9:00 PM (school hours).';
    }
}
