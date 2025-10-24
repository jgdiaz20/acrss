<?php

namespace App\Http\Requests;

use App\AcademicProgram;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdateAcademicProgramRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('academic_program_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:academic_programs,code,' . request()->route('academic_program')->id,
            ],
            'type' => [
                'required',
                'in:senior_high,diploma,college',
                function ($attribute, $value, $fail) {
                    $program = request()->route('academic_program');
                    $oldType = $program->type;
                    
                    // If changing FROM diploma TO non-diploma (senior_high or college)
                    if ($oldType === 'diploma' && $value !== 'diploma') {
                        // Check for weekend lessons (Saturday=6, Sunday=7)
                        $weekendLessons = \App\Lesson::whereHas('class', function($q) use ($program) {
                            $q->where('program_id', $program->id);
                        })
                        ->whereIn('weekday', [6, 7])
                        ->with(['class', 'subject'])
                        ->get();
                        
                        if ($weekendLessons->count() > 0) {
                            $newTypeName = $this->getProgramTypeName($value);
                            $lessonCount = $weekendLessons->count();
                            
                            // Build detailed error message with lesson examples
                            $lessonDetails = $weekendLessons->take(3)->map(function($lesson) {
                                $day = $lesson->weekday == 6 ? 'Saturday' : 'Sunday';
                                $className = $lesson->class ? $lesson->class->name : 'Unknown Class';
                                $subjectName = $lesson->subject ? $lesson->subject->name : 'Unknown Subject';
                                return "• {$className} - {$subjectName} ({$day})";
                            })->implode("\n");
                            
                            $moreText = $lessonCount > 3 ? "\n...and " . ($lessonCount - 3) . " more." : '';
                            
                            $fail("Cannot change program type from Diploma to {$newTypeName}. There are {$lessonCount} lesson(s) scheduled on weekends (Saturday/Sunday):\n\n{$lessonDetails}{$moreText}\n\nWeekend classes are only allowed for Diploma Programs. Please delete or reschedule these lessons to weekdays (Monday-Friday) before changing the program type.");
                        }
                    }
                },
            ],
            'duration_years' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }
    
    /**
     * Get human-readable program type name
     */
    private function getProgramTypeName($type)
    {
        $typeNames = [
            'senior_high' => 'Senior High School',
            'college' => 'College',
            'diploma' => 'Diploma Program (TESDA)',
        ];
        
        return $typeNames[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }
}