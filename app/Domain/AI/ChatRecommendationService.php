<?php

namespace App\Domain\AI;

use App\Domain\Scheduling\Actions\EvaluateSchedulingAction;
use App\Domain\Scheduling\DTO\RescheduleRequest;
use App\Domain\Scheduling\Enums\Scope;
use App\Models\Schedule;

class ChatRecommendationService
{
    public function __construct(
        private readonly EvaluateSchedulingAction $evaluateAction,
        private readonly LlmClient $llmClient,
    ) {}

    /**
     * @return array{reply: string, slots: list<array>, source: string, engineRan: bool, engineSteps: list<string>}
     */
    public function process(string $message, ?string $scheduleId = null, string $scope = 'once', ?string $targetDate = null): array
    {
        $engineRan = false;
        $slots = [];
        $engineStepTitles = [];

        if ($scheduleId && Schedule::where('id', $scheduleId)->whereNull('deleted_at')->exists()) {
            $engineRan = true;
            $rescheduleRequest = new RescheduleRequest(
                scheduleId: $scheduleId,
                scope: Scope::tryFrom($scope) ?? Scope::ONCE,
                target: $targetDate ?? now()->toDateString(),
            );

            $result = $this->evaluateAction->execute($rescheduleRequest);
            $rawOptions = $result['options'] ?? [];
            $engineStepTitles = array_map(fn ($s) => $s['title'] ?? '', $result['steps'] ?? []);

            $top3 = array_slice($rawOptions, 0, 3);
            foreach ($top3 as $opt) {
                $slots[] = [
                    'day' => $opt['day'],
                    'time' => "{$opt['start_time']} - {$opt['end_time']}",
                    'room' => $opt['room_name'],
                    'roomId' => $opt['room_id'],
                    'score' => $opt['score'],
                    'note' => "Skor {$opt['score']}",
                ];
            }
        }

        $systemPrompt = "Anda adalah Asisten AI Akademik resmi PENSCEDULER Politeknik Elektronika Negeri Surabaya.\n"
            . "Tugas Anda memberikan jawaban ramah, jelas, dan profesional dalam bahasa Indonesia.\n"
            . "ATURAN MUTLAK:\n"
            . "- Algoritma bitmask adalah sumber kebenaran tunggal untuk rekomendasi waktu dan ruangan.\n"
            . "- Dilarang keras mengarang atau menambahkan waktu, ruangan, atau jadwal di luar data yang diberikan.\n"
            . "- Jika terdapat slot rekomendasi yang diberikan, jelaskan opsi tersebut dengan ringkas.\n"
            . "- Jika tidak ada slot yang tersedia atau jadwal tidak dipilih, pandu pengguna untuk memilih matakuliah terkait.";

        $contextData = $engineRan
            ? "Hasil perhitungan algoritma bitmask bebas konflik:\n" . json_encode($slots, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : "Tidak ada jadwal spesifik yang dipilih untuk evaluasi bentrok bitmask.";

        $userPrompt = "Pesan pengguna:\n<user_message>\n{$message}\n</user_message>\n\nKonteks sistem:\n{$contextData}";

        $llmReply = $this->llmClient->generate($systemPrompt, $userPrompt);
        $source = 'llm';

        if (! $llmReply) {
            $source = 'template';
            $llmReply = $this->formatTemplateReply($message, $slots, $engineRan);
        }

        return [
            'reply' => $llmReply,
            'slots' => $slots,
            'source' => $source,
            'engineRan' => $engineRan,
            'engineSteps' => array_values(array_filter($engineStepTitles)),
        ];
    }

    private function formatTemplateReply(string $message, array $slots, bool $engineRan): string
    {
        if (! $engineRan) {
            return "Halo. Saya Asisten AI Akademik PENSCEDULER. Untuk mendapatkan rekomendasi slot perkuliahan bebas konflik secara otomatis menggunakan bitmask engine, silakan pilih matakuliah yang ingin disesuaikan atau tanyakan informasi jadwal perkuliahan.";
        }

        if (empty($slots)) {
            return "Algoritma bitmask telah memeriksa matriks jadwal kampus, namun tidak menemukan slot kosong yang memenuhi kriteria bebas bentrok untuk parameter yang diajukan.";
        }

        $count = count($slots);
        $text = "Berdasarkan evaluasi algoritma bitmask penjadwalan, ditemukan {$count} slot rekomendasi terbaik bebas bentrok:\n";
        foreach ($slots as $idx => $slot) {
            $num = $idx + 1;
            $text .= "{$num}. {$slot['day']} ({$slot['time']}) di {$slot['room']} [{$slot['note']}]\n";
        }
        $text .= "\nAnda dapat langsung menerapkan slot pilihan ke formulir pemindahan jadwal.";

        return trim($text);
    }
}
