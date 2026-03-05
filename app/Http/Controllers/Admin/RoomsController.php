<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRoomRequest;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Room;
use App\Services\QRCodeService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoomsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('room_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = Room::query();

        // Filter by type (lab/classroom)
        if ($request->filled('type')) {
            if ($request->type === 'lab') {
                $query->where('is_lab', true);
            } elseif ($request->type === 'classroom') {
                $query->where('is_lab', false);
            }
        }

        // Filter by capacity range
        if ($request->filled('capacity_min')) {
            $query->where('capacity', '>=', $request->capacity_min);
        }
        if ($request->filled('capacity_max')) {
            $query->where('capacity', '<=', $request->capacity_max);
        }

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $rooms = $query->paginate($perPage);

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        abort_if(Gate::denies('room_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.rooms.create');
    }

    public function store(StoreRoomRequest $request)
    {
        $data = $request->all();
        
        // Handle boolean fields properly
        $data['is_lab'] = $request->has('is_lab') ? (bool) $request->input('is_lab') : false;
        $data['has_equipment'] = false; // Equipment field removed from UI
        
        $room = Room::create($data);

        return redirect()->route('admin.room-management.rooms.index')
            ->with('success', 'Room created successfully!');
    }

    public function edit(Room $room)
    {
        abort_if(Gate::denies('room_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.rooms.edit', compact('room'));
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        $data = $request->all();
        
        // Handle boolean fields properly
        $data['is_lab'] = $request->has('is_lab') ? (bool) $request->input('is_lab') : false;
        $data['has_equipment'] = false; // Equipment field removed from UI
        
        $room->update($data);

        return redirect()->route('admin.room-management.rooms.index')
            ->with('success', 'Room updated successfully!');
    }

    public function show(Room $room)
    {
        abort_if(Gate::denies('room_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $room->load('lessons.teacher', 'lessons.class');

        return view('admin.rooms.show', compact('room'));
    }

    public function destroy(Room $room)
    {
        abort_if(Gate::denies('room_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Check if room has lessons
        if ($room->lessons()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete room with existing lessons. Please remove all lessons first.']);
        }

        $roomName = $room->name; // Store name before deletion for message
        
        // Hard delete the room - completely remove from database
        $room->forceDelete();

        return back()->with('success', "Room '{$roomName}' has been successfully deleted!");
    }

    public function massDestroy(MassDestroyRoomRequest $request)
    {
        $roomIds = (array) $request->input('ids', []);

        // Load selected rooms as a collection to avoid mutating a base query
        $rooms = Room::whereIn('id', $roomIds)->get();

        // Validate that all requested rooms exist
        if ($rooms->count() !== count($roomIds)) {
            return response()->json(['error' => 'One or more selected rooms do not exist.'], 422);
        }

        // Ensure none of the rooms have lessons assigned (all-or-nothing)
        $roomsWithLessons = $rooms->filter(function ($room) {
            return $room->lessons()->exists();
        });

        if ($roomsWithLessons->isNotEmpty()) {
            $blockedNames = $roomsWithLessons->pluck('name')->values()->all();
            return response()->json([
                'error' => 'Deletion blocked. The following rooms have lessons assigned: ' . implode(', ', $blockedNames)
            ], 422);
        }

        // Collect names for success message before deletion
        $roomNames = $rooms->pluck('name')->values()->all();
        $roomCount = count($roomNames);

        // Perform hard delete (permanent)
        Room::whereIn('id', $roomIds)->forceDelete();

        return response()->json([
            'message' => $roomCount === 1
                ? "Room '{$roomNames[0]}' has been successfully deleted!"
                : "{$roomCount} rooms have been successfully deleted!"
        ], Response::HTTP_OK);
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
