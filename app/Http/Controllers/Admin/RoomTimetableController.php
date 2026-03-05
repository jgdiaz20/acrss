<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Room;
use App\Services\RoomCalendarService;
use App\Services\QRCodeService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoomTimetableController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('room_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rooms = Room::all();

        return view('admin.room-timetable.index', compact('rooms'));
    }

    public function show(Room $room, RoomCalendarService $roomCalendarService)
    {
        abort_if(Gate::denies('room_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $weekDays = \App\Lesson::WEEK_DAYS;
        
        // Generate time-based timetable matrix
        $timetableData = $roomCalendarService->generateRoomTimetableMatrix($room, $weekDays);

        return view('admin.room-timetable.show', compact('room', 'weekDays', 'timetableData'));
    }

    /**
     * Show QR code for a specific room
     */
    public function showQRCode(Room $room, QRCodeService $qrCodeService)
    {
        abort_if(Gate::denies('room_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $qrCodeData = $qrCodeService->getRoomQRCodeData($room);

        return response()->json($qrCodeData);
    }

    /**
     * Generate QR codes for all rooms
     */
    public function generateAllQRCodes(QRCodeService $qrCodeService)
    {
        abort_if(Gate::denies('room_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rooms = Room::all();
        $qrCodesData = $qrCodeService->generateMultipleRoomQRCodes($rooms);

        return response()->json($qrCodesData);
    }
}
