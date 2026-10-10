<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $students = Student::whereNull('deleted_at')->orderBy('username')->get();

        return StudentResource::collection($students);
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        if (! isset($input['username']) && isset($input['name'])) {
            $input['username'] = $input['name'];
        }

        $validator = validator($input, [
            'username' => ['required', 'string', 'max:150'],
            'class' => ['required', 'string', 'max:50'],
            'nrp' => ['required', 'string', 'max:30', Rule::unique('student', 'nrp')->whereNull('deleted_at')],
            'cohort_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'department' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
        ]);

        $validated = $validator->validate();

        $student = Student::create([
            'username' => $validated['username'],
            'class' => $validated['class'],
            'nrp' => $validated['nrp'],
            'cohort_year' => $validated['cohort_year'] ?? null,
            'department' => $validated['department'] ?? '',
            'email' => $validated['email'] ?? null,
        ]);

        return (new StudentResource($student))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Student $student): StudentResource
    {
        $input = $request->all();
        if (! isset($input['username']) && isset($input['name'])) {
            $input['username'] = $input['name'];
        }

        $validator = validator($input, [
            'username' => ['required', 'string', 'max:150'],
            'class' => ['required', 'string', 'max:50'],
            'nrp' => ['required', 'string', 'max:30', Rule::unique('student', 'nrp')->ignore($student->id)->whereNull('deleted_at')],
            'cohort_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'department' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
        ]);

        $validated = $validator->validate();

        $student->update([
            'username' => $validated['username'],
            'class' => $validated['class'],
            'nrp' => $validated['nrp'],
            'cohort_year' => $validated['cohort_year'] ?? $student->cohort_year,
            'department' => $validated['department'] ?? $student->department,
            'email' => $validated['email'] ?? $student->email,
        ]);

        return new StudentResource($student);
    }

    public function destroy(Student $student): Response
    {
        $student->delete();

        return response()->noContent();
    }
}
