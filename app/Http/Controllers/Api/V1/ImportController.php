<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Import\CsvImporter;
use App\Http\Controllers\Controller;
use App\Services\AuditTrail;
use App\Services\DemoActorResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function __construct(
        private readonly CsvImporter $importer,
        private readonly DemoActorResolver $actorResolver,
        private readonly AuditTrail $auditTrail,
    ) {}

    public function store(Request $request, string $entity): JsonResponse
    {
        if (! in_array($entity, ['rooms', 'subjects', 'lecturers', 'students', 'schedules'], true)) {
            return response()->json(['message' => 'Entitas impor tidak valid.'], 404);
        }

        $input = null;
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            ]);
            $input = $request->file('file');
        } elseif ($request->has('rows')) {
            $request->validate([
                'rows' => ['required', 'array', 'min:1'],
                'rows.*' => ['required', 'array'],
            ]);
            $input = $request->input('rows');
        } else {
            return response()->json([
                'message' => 'Harap sertakan berkas CSV (file) atau data baris (rows).',
            ], 422);
        }

        $result = $this->importer->import($entity, $input);

        $currentRole = $this->actorResolver->roleFor($request);
        $user = $this->actorResolver->userFor($currentRole);

        $status = $result['failed'] > 0 ? 'Sebagian' : 'Selesai';
        $this->auditTrail->record(
            level: 'sync',
            module: 'Data Ingestion',
            action: "import_{$entity}",
            details: "Impor {$entity}: {$result['imported']} dari {$result['total']} baris berhasil.",
            actor: $user,
            status: $status,
        );

        return response()->json($result);
    }
}
