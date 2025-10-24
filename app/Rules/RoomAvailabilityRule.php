<?php

namespace App\Rules;

use App\Room;
use App\Subject;
use Illuminate\Contracts\Validation\Rule;

class RoomAvailabilityRule implements Rule
{
    protected $subjectId;
    protected $roomId;
    protected $message;

    public function __construct($subjectId = null)
    {
        $this->subjectId = $subjectId;
    }

    public function passes($attribute, $value)
    {
        $this->roomId = $value;
        
        // If no room selected, let required validation handle it
        if (!$value) {
            return true;
        }

        $room = Room::find($value);
        if (!$room) {
            $this->message = 'The selected room does not exist.';
            return false;
        }

        // If no subject specified, any room is valid
        if (!$this->subjectId) {
            return true;
        }

        $subject = Subject::find($this->subjectId);
        if (!$subject) {
            return true;
        }

        // Labs and equipped rooms can be used for any subject
        if ($room->is_lab || $room->has_equipment) {
            return true;
        }

        // Regular rooms can be used for any subject
        return true;
    }

    public function message()
    {
        return $this->message ?? 'The selected room is not suitable for this subject.';
    }
}