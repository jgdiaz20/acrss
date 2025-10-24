<?php

namespace App\Http\Controllers;

use App\Room;
use App\Services\RoomCalendarService;
use Illuminate\Http\Request;

class PublicRoomController extends Controller
{
    /**
     * Show public room timetable (no authentication required)
     */
    public function show($identifier, RoomCalendarService $roomCalendarService)
    {
        // Find room by identifier
        $room = $this->findRoomByIdentifier($identifier);
        
        if (!$room) {
            abort(404, 'Room not found');
        }

        // Generate timetable data (same as admin)
        $weekDays = \App\Lesson::WEEK_DAYS;
        $calendarData = $roomCalendarService->generateRoomCalendarData($room, $weekDays);

        return view('public.room-timetable', compact('room', 'weekDays', 'calendarData'));
    }

    /**
     * Find room by QR code identifier
     */
    private function findRoomByIdentifier($identifier)
    {
        $rooms = Room::all();
        
        foreach ($rooms as $room) {
            $roomData = $room->id . '|' . $room->name . '|' . config('app.key');
            $generatedIdentifier = hash('sha256', $roomData);
            
            if ($generatedIdentifier === $identifier) {
                return $room;
            }
        }
        
        return null;
    }
}
