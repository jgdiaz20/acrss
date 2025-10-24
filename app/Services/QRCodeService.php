<?php

namespace App\Services;

use App\Room;

class QRCodeService
{
    /**
     * Generate unique identifier for room QR code
     */
    public function generateRoomIdentifier(Room $room)
    {
        $roomData = $room->id . '|' . $room->name . '|' . config('app.key');
        return hash('sha256', $roomData);
    }

    /**
     * Generate QR code image URL with fallback system
     * Primary: QuickChart API (most reliable)
     * Fallback 1: Google Charts API
     * Fallback 2: QR Server API
     */
    public function generateQRCodeImage($identifier, $size = 200)
    {
        $publicUrl = route('public.room.timetable', ['identifier' => $identifier]);
        
        // Primary: QuickChart API (most reliable and actively maintained)
        $quickChartUrl = "https://quickchart.io/qr?text=" . urlencode($publicUrl) . "&size={$size}x{$size}";
        
        // Test if QuickChart API is accessible
        if ($this->isQuickChartAccessible()) {
            return $quickChartUrl;
        }
        
        // Fallback 1: Google Charts API
        $googleChartsUrl = "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl=" . urlencode($publicUrl);
        if ($this->isGoogleChartsAccessible()) {
            return $googleChartsUrl;
        }
        
        // Fallback 2: QR Server API
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($publicUrl);
    }
    
    /**
     * Check if QuickChart API is accessible
     */
    private function isQuickChartAccessible()
    {
        $testUrl = "https://quickchart.io/qr?text=test&size=10x10";
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 8, // Slightly longer timeout for better reliability
                'ignore_errors' => true,
                'user_agent' => 'Laravel-QRCodeService/1.0'
            ]
        ]);
        
        $response = @file_get_contents($testUrl, false, $context);
        return $response !== false && strlen($response) > 50; // QuickChart QR images are typically smaller
    }
    
    /**
     * Check if Google Charts API is accessible
     */
    private function isGoogleChartsAccessible()
    {
        $testUrl = "https://chart.googleapis.com/chart?chs=10x10&cht=qr&chl=test";
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 8, // Increased timeout for better reliability
                'ignore_errors' => true,
                'user_agent' => 'Laravel-QRCodeService/1.0'
            ]
        ]);
        
        $response = @file_get_contents($testUrl, false, $context);
        return $response !== false && strlen($response) > 100; // Valid QR image should be > 100 bytes
    }

    /**
     * Get complete QR code data for a room
     */
    public function getRoomQRCodeData(Room $room)
    {
        $identifier = $this->generateRoomIdentifier($room);
        $qrImageUrl = $this->generateQRCodeImage($identifier);
        $publicUrl = route('public.room.timetable', ['identifier' => $identifier]);

        return [
            'room' => $room,
            'identifier' => $identifier,
            'qr_image_url' => $qrImageUrl,
            'public_url' => $publicUrl,
            'qr_code_data' => base64_encode($publicUrl) // For direct QR generation if needed
        ];
    }

    /**
     * Generate QR code for multiple rooms
     */
    public function generateMultipleRoomQRCodes($rooms)
    {
        $qrCodes = [];
        
        foreach ($rooms as $room) {
            $qrCodes[] = $this->getRoomQRCodeData($room);
        }

        return $qrCodes;
    }
}
