<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use App\Models\Building;
use App\Models\Room;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $rooms = Room::with('building')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return RoomResource::collection($rooms);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('room', 'code')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'building_id' => ['nullable', 'uuid', 'exists:buildings,id'],
            'building_code' => ['nullable', 'string', 'max:30'],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'type' => ['nullable', 'string', 'in:teori,lab,aula'],
            'floor' => ['nullable', 'integer', 'min:1', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $buildingId = $this->resolveBuildingId($validated);
        if (! $buildingId) {
            return response()->json(['message' => 'Gedung tidak valid atau tidak ditemukan.'], 422);
        }

        $room = Room::create([
            'building_id' => $buildingId,
            'code' => $validated['code'],
            'name' => $validated['name'],
            'capacity' => $validated['capacity'],
            'type' => $validated['type'] ?? 'teori',
            'floor' => $validated['floor'] ?? 1,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return (new RoomResource($room->load('building')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Room $room): RoomResource|JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('room', 'code')->ignore($room->id)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'building_id' => ['nullable', 'uuid', 'exists:buildings,id'],
            'building_code' => ['nullable', 'string', 'max:30'],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'type' => ['nullable', 'string', 'in:teori,lab,aula'],
            'floor' => ['nullable', 'integer', 'min:1', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $buildingId = $this->resolveBuildingId($validated) ?? $room->building_id;

        $room->update([
            'building_id' => $buildingId,
            'code' => $validated['code'],
            'name' => $validated['name'],
            'capacity' => $validated['capacity'],
            'type' => $validated['type'] ?? $room->type,
            'floor' => $validated['floor'] ?? $room->floor,
            'is_active' => $validated['is_active'] ?? $room->is_active,
        ]);

        return new RoomResource($room->load('building'));
    }

    public function destroy(Room $room): Response|JsonResponse
    {
        $isReferenced = Schedule::where('roomId', $room->id)->whereNull('deleted_at')->exists();
        if ($isReferenced) {
            return response()->json([
                'message' => 'Ruangan tidak dapat dihapus karena masih digunakan dalam jadwal perkuliahan aktif.',
            ], 409);
        }

        $room->delete();

        return response()->noContent();
    }

    private function resolveBuildingId(array $data): ?string
    {
        if (! empty($data['building_id'])) {
            return $data['building_id'];
        }

        if (! empty($data['building_code'])) {
            $building = Building::where('code', $data['building_code'])->first();
            if ($building) {
                return $building->id;
            }
        }

        return Building::first()?->id;
    }
}
