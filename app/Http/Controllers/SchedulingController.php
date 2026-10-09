<?php

namespace App\Http\Controllers;

use App\Domain\Scheduling\Actions\EvaluateSchedulingAction;
use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\Enums\Scope;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchedulingController extends Controller
{
    public function evaluate(Request $request, EvaluateSchedulingAction $action)
    {
        $validated = $request->validate([
            'scheduleId' => 'required|string',
            'scope' => 'required|in:once,onwards',
            'target' => 'required|date',
        ]);

        $rescheduleRequest = new RescheduleRequest(
            scheduleId: $validated['scheduleId'],
            scope: Scope::from($validated['scope']),
            target: $validated['target'],
        );

        $result = $action->execute($rescheduleRequest);

        return response()->json($result);
    }

    public function evaluateStream(Request $request, EvaluateSchedulingAction $action): StreamedResponse
    {
        $validated = $request->validate([
            'scheduleId' => 'required|string',
            'scope' => 'required|in:once,onwards',
            'target' => 'required|date',
        ]);

        $rescheduleRequest = new RescheduleRequest(
            scheduleId: $validated['scheduleId'],
            scope: Scope::from($validated['scope']),
            target: $validated['target'],
        );

        return new StreamedResponse(function () use ($action, $rescheduleRequest) {
            foreach ($action->executeStream($rescheduleRequest) as $data) {
                echo "data: " . json_encode($data) . "\n\n";
                ob_flush();
                flush();
                usleep(100000);
            }

            echo "event: done\ndata: \n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
