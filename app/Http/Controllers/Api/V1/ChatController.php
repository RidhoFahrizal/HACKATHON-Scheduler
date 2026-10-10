<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\AI\ChatRecommendationService;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\AuditTrail;
use App\Services\DemoActorResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatRecommendationService $chatService,
        private readonly DemoActorResolver $actorResolver,
        private readonly AuditTrail $auditTrail,
    ) {}

    public function message(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'sessionId' => ['nullable', 'uuid', 'exists:chat_sessions,id'],
            'scheduleId' => ['nullable', 'uuid', 'exists:schedule,id'],
            'scope' => ['nullable', 'string', 'in:once,onwards'],
            'target' => ['nullable', 'date'],
        ]);

        $currentRole = $this->actorResolver->roleFor($request);
        $user = $this->actorResolver->userFor($currentRole);

        $session = null;
        if (! empty($validated['sessionId'])) {
            $session = ChatSession::find($validated['sessionId']);
        }

        if (! $session) {
            $title = Str::limit(trim($validated['message']), 40, '...');
            $session = ChatSession::create([
                'user_id' => $user->id,
                'title' => $title ?: 'Konsultasi Jadwal',
            ]);
        }

        $userMsg = $session->messages()->create([
            'sender_type' => 'user',
            'message' => $validated['message'],
        ]);

        $res = $this->chatService->process(
            message: $validated['message'],
            scheduleId: $validated['scheduleId'] ?? null,
            scope: $validated['scope'] ?? 'once',
            targetDate: $validated['target'] ?? null,
        );

        $aiMsg = $session->messages()->create([
            'sender_type' => 'ai',
            'message' => $res['reply'],
            'suggested_slots_payload' => $res['slots'],
        ]);

        $this->auditTrail->record(
            level: 'info',
            module: 'Asisten AI',
            action: 'chat_query',
            details: "Interaksi chat pada sesi {$session->id} (Engine: " . ($res['engineRan'] ? 'Aktif' : 'Nonaktif') . ")",
            actor: $user,
            status: 'Sukses',
        );

        return response()->json([
            'sessionId' => $session->id,
            'reply' => $res['reply'],
            'slots' => $res['slots'],
            'source' => $res['source'],
            'engineRan' => $res['engineRan'],
            'engineSteps' => $res['engineSteps'],
            'userMessageId' => $userMsg->id,
            'assistantMessageId' => $aiMsg->id,
        ]);
    }
}
