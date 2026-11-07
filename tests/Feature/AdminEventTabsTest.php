<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventTabsTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_page_uses_free_tab_navigation(): void
    {
        $event = Event::factory()->create();

        $response = $this->get(route('admin.events.edit', $event));

        $response->assertOk();
        $response->assertSee('data-wizard-mode="tabs"', false);
        $response->assertSee('data-step-indicator="1"', false);
        $response->assertSee('data-step-indicator="4"', false);
        // Indikator langkah bisa diklik sebagai tab.
        $response->assertSee('tabindex="0"', false);
        $response->assertSee('role="button"', false);
    }

    public function test_create_page_keeps_linear_wizard_mode(): void
    {
        $response = $this->get(route('admin.events.create'));

        $response->assertOk();
        $response->assertSee('data-wizard-mode="wizard"', false);
        // Di mode wizard, indikator bukan tombol (tidak bisa dilompati).
        $this->assertStringNotContainsString('data-wizard-mode="wizard" role', $response->getContent());
    }

    public function test_edit_page_keeps_all_four_step_panes_in_one_form(): void
    {
        $event = Event::factory()->create();

        $response = $this->get(route('admin.events.edit', $event));

        $response->assertOk();

        $content = $response->getContent();
        $this->assertSame(1, substr_count($content, 'data-step="1"'));
        $this->assertSame(1, substr_count($content, 'data-step="4"'));
        // Form utama tetap satu; form lain hanya modal tambah kategori (di luar form utama).
        $this->assertSame(1, substr_count($content, 'id="eventWizard"'));
    }
}
