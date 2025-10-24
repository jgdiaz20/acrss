<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheInvalidationService
{
    /**
     * Clear teacher assignment related caches
     */
    public static function clearTeacherAssignmentCaches($subjectId = null)
    {
        if ($subjectId) {
            Cache::forget("teachers_for_subject_{$subjectId}");
        }
        
        // Clear individual subject caches
        Cache::forget("teachers_for_subject_");
        
        // For cache stores that don't support tagging, we'll flush all cache
        // This is safe since we're only caching teacher assignments temporarily
        try {
            Cache::flush();
        } catch (\Exception $e) {
            \Log::warning('Failed to flush cache during teacher assignment update', [
                'error' => $e->getMessage(),
                'subject_id' => $subjectId
            ]);
        }
    }

    /**
     * Clear room assignment related caches
     */
    public static function clearRoomAssignmentCaches($subjectId = null)
    {
        if ($subjectId) {
            Cache::forget("rooms_for_subject_{$subjectId}");
        }
        
        // For cache stores that don't support tagging, we'll flush all cache
        try {
            Cache::flush();
        } catch (\Exception $e) {
            \Log::warning('Failed to flush cache during room assignment update', [
                'error' => $e->getMessage(),
                'subject_id' => $subjectId
            ]);
        }
    }

    /**
     * Clear all subject-related caches
     */
    public static function clearSubjectCaches($subjectId = null)
    {
        if ($subjectId) {
            Cache::forget("subject_details_{$subjectId}");
            Cache::forget("subject_lessons_{$subjectId}");
        }
        
        self::clearTeacherAssignmentCaches($subjectId);
        self::clearRoomAssignmentCaches($subjectId);
    }

    /**
     * Clear lesson-related caches
     */
    public static function clearLessonCaches($lessonId = null)
    {
        if ($lessonId) {
            Cache::forget("lesson_details_{$lessonId}");
        }
        
        // For cache stores that don't support tagging, we'll flush all cache
        try {
            Cache::flush();
        } catch (\Exception $e) {
            \Log::warning('Failed to flush cache during lesson update', [
                'error' => $e->getMessage(),
                'lesson_id' => $lessonId
            ]);
        }
    }

    /**
     * Clear all scheduling-related caches
     */
    public static function clearAllSchedulingCaches()
    {
        self::clearTeacherAssignmentCaches();
        self::clearRoomAssignmentCaches();
        self::clearLessonCaches();
    }
}
