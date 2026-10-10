<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectResource;
use App\Models\Schedule;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $subjects = Subject::whereNull('deleted_at')->orderBy('name')->get();

        return SubjectResource::collection($subjects);
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        if (! isset($input['credits']) && isset($input['sks'])) {
            $input['credits'] = $input['sks'];
        }

        $validator = validator($input, [
            'code' => ['required', 'string', 'max:30', Rule::unique('subject', 'code')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'min:1', 'max:6'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'department' => ['nullable', 'string', 'max:100'],
            'lecturerId' => ['nullable', 'uuid', 'exists:lecturer,id'],
        ]);

        $validated = $validator->validate();

        $subject = Subject::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'credits' => $validated['credits'],
            'semester' => $validated['semester'] ?? null,
            'department' => $validated['department'] ?? '',
            'lecturerId' => $validated['lecturerId'] ?? null,
        ]);

        return (new SubjectResource($subject))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Subject $subject): SubjectResource
    {
        $input = $request->all();
        if (! isset($input['credits']) && isset($input['sks'])) {
            $input['credits'] = $input['sks'];
        }

        $validator = validator($input, [
            'code' => ['required', 'string', 'max:30', Rule::unique('subject', 'code')->ignore($subject->id)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'min:1', 'max:6'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'department' => ['nullable', 'string', 'max:100'],
            'lecturerId' => ['nullable', 'uuid', 'exists:lecturer,id'],
        ]);

        $validated = $validator->validate();

        $subject->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'credits' => $validated['credits'],
            'semester' => $validated['semester'] ?? $subject->semester,
            'department' => $validated['department'] ?? $subject->department,
            'lecturerId' => array_key_exists('lecturerId', $validated) ? $validated['lecturerId'] : $subject->lecturerId,
        ]);

        return new SubjectResource($subject);
    }

    public function destroy(Subject $subject): Response|JsonResponse
    {
        $isReferenced = Schedule::where('subjectId', $subject->id)->whereNull('deleted_at')->exists();
        if ($isReferenced) {
            return response()->json([
                'message' => 'Subjek tidak dapat dihapus karena masih digunakan dalam jadwal perkuliahan aktif.',
            ], 409);
        }

        $subject->delete();

        return response()->noContent();
    }
}
