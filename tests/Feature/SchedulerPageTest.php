<?php

namespace Tests\Feature;

use Tests\TestCase;

class SchedulerPageTest extends TestCase
{
    private const REQUIRED_ELEMENT_IDS = [
        'view-dashboard',
        'view-classes',
        'view-class-detail',
        'view-schedule',
        'view-reschedule',
        'view-chat',
        'view-baak-requests',
        'view-baak-rooms',
        'view-baak-subjects',
        'view-baak-lecturers',
        'view-baak-students',
        'view-baak-system-logs',
        'recommendations-overlay-modal',
        'csv-import-modal',
        'baak-crud-modal',
    ];

    public function test_index_renders_every_view_and_modal_container(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        foreach (self::REQUIRED_ELEMENT_IDS as $id) {
            $response->assertSee('id="'.$id.'"', false);
        }
    }

    public function test_index_defaults_to_the_student_role(): void
    {
        $this->get('/')
            ->assertSee($this->embeddedRole('mahasiswa'), false);
    }

    public function test_index_embeds_the_role_stored_in_the_session(): void
    {
        $this->withSession(['role' => 'baak'])
            ->get('/')
            ->assertSee($this->embeddedRole('baak'), false);
    }

    public function test_switch_role_stores_the_role_in_the_session(): void
    {
        $this->postJson('/switch-role', ['role' => 'dosen'])
            ->assertNoContent()
            ->assertSessionHas('role', 'dosen');
    }

    public function test_switch_role_rejects_an_unknown_role(): void
    {
        $this->postJson('/switch-role', ['role' => 'admin'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('role');
    }

    public function test_switch_role_requires_a_role(): void
    {
        $this->postJson('/switch-role', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('role');
    }

    /**
     * Js::from encodes the payload twice, so every double quote reaches the page as a hex escape inside the
     * JSON.parse('...') literal.
     */
    private function embeddedRole(string $role): string
    {
        $quote = chr(92).'u0022';

        return $quote.'activeRole'.$quote.':'.$quote.$role.$quote;
    }
}
