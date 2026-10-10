<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LecturerResource;
use App\Models\Lecturer;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class LecturerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $lecturers = Lecturer::whereNull('deleted_at')->orderBy('username')->get();

        return LecturerResource::collection($lecturers);
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        if (! isset($input['username']) && isset($input['name'])) {
            $input['username'] = $input['name'];
        }

        $validator = validator($input, [
            'username' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'nip' => ['required', 'string', 'max:30', Rule::unique('lecturer', 'nip')->whereNull('deleted_at')],
            'code' => ['nullable', 'string', 'max:30', Rule::unique('lecturer', 'code')->whereNull('deleted_at')],
            'academic_title' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
        ]);

        $validated = $validator->validate();

        $lecturer = Lecturer::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'nip' => $validated['nip'],
            'code' => $validated['code'] ?? null,
            'academic_title' => $validated['academic_title'] ?? null,
            'department' => $validated['department'] ?? '',
        ]);

        return (new LecturerResource($lecturer))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Lecturer $lecturer): LecturerResource
    {
        $input = $request->all();
        if (! isset($input['username']) && isset($input['name'])) {
            $input['username'] = $input['name'];
        }

        $validator = validator($input, [
            'username' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'nip' => ['required', 'string', 'max:30', Rule::unique('lecturer', 'nip')->ignore($lecturer->id)->whereNull('deleted_at')],
            'code' => ['nullable', 'string', 'max:30', Rule::unique('lecturer', 'code')->ignore($lecturer->id)->whereNull('deleted_at')],
            'academic_title' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
        ]);

        $validated = $validator->validate();

        $lecturer->update([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'nip' => $validated['nip'],
            'code' => $validated['code'] ?? $lecturer->code,
            'academic_title' => $validated['academic_title'] ?? $lecturer->academic_title,
            'department' => $validated['department'] ?? $lecturer->department,
        ]);

        return new LecturerResource($lecturer);
    }

    public function destroy(Lecturer $lecturer): Response|JsonResponse
    {
        $isReferenced = Schedule::where('lecturerId', $lecturer->id)->whereNull('deleted_at')->exists();
        if ($isReferenced) {
            return response()->json([
                'message' => 'Dosen tidak dapat dihapus karena masih mengampu jadwal perkuliahan aktif.',
            ], 409);
        }

        $lecturer->delete();

        return response()->noContent();
    }
}
