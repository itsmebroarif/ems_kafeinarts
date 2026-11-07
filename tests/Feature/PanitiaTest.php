<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCommittee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PanitiaTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
        $this->event = Event::factory()->create();
    }

    public function test_profile_edit_page_renders_for_admin(): void
    {
        $response = $this->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('Edit Profil');
        $response->assertSee('Ganti Password');
    }

    public function test_admin_can_update_profile_with_phone_and_social_media(): void
    {
        $response = $this->put(route('profile.update'), [
            'name' => 'Admin Baru',
            'email' => 'adminbarubaru@kafeinarts.test',
            'phone' => '081234567890',
            'instagram' => 'kafeinarts',
            'website' => 'kafeinarts.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Admin Baru',
            'phone' => '081234567890',
            'instagram' => 'https://instagram.com/kafeinarts',
            'website' => 'https://kafeinarts.com',
        ]);
    }

    public function test_admin_can_add_existing_user_as_committee_member(): void
    {
        $panitia = User::factory()->create(['name' => 'Andi Panitia', 'email' => 'andi@panitia.test']);

        $response = $this->post(route('admin.events.panitia.store', $this->event->id), [
            'mode' => 'existing',
            'user_id' => $panitia->id,
            'role' => 'sekretaris',
            'joined_at' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('admin.events.panitia.index', $this->event));
        $this->assertDatabaseHas('event_committees', [
            'event_id' => $this->event->id,
            'user_id' => $panitia->id,
            'role' => 'sekretaris',
        ]);
    }

    public function test_admin_can_register_new_panitia_account_with_role(): void
    {
        $response = $this->post(route('admin.events.panitia.store', $this->event->id), [
            'mode' => 'new',
            'name' => 'Panitia Baru',
            'email' => 'panitianbaru@test.test',
            'phone' => '081111111111',
            'password' => 'secret123',
            'role' => 'ketua',
        ]);

        $response->assertRedirect(route('admin.events.panitia.index', $this->event));
        $this->assertDatabaseHas('users', ['email' => 'panitianbaru@test.test', 'name' => 'Panitia Baru']);
        $this->assertDatabaseHas('event_committees', ['role' => 'ketua']);
    }

    public function test_admin_can_update_member_role(): void
    {
        $member = $this->makeCommitteeMember();

        $response = $this->put(route('admin.events.panitia.role', [$this->event->id, $member->id]), [
            'role' => 'bendahara',
        ]);

        $response->assertRedirect();
        $this->assertSame('bendahara', $member->fresh()->role);
    }

    public function test_payroll_computes_net_salary(): void
    {
        $member = $this->makeCommitteeMember();

        $response = $this->post(route('admin.events.panitia.payroll.store', [$this->event->id, $member->id]), [
            'base_salary' => 1000000,
            'allowance' => 200000,
            'overtime' => 150000,
            'deduction' => 50000,
            'period_start' => '2026-11-01',
            'period_end' => '2026-11-30',
        ]);

        $response->assertRedirect();

        $payroll = $member->payrolls()->first();
        $this->assertSame('1300000.00', (string) $payroll->net_salary);
        $this->assertSame('draft', $payroll->status);
    }

    public function test_admin_can_mark_payroll_as_paid_and_back(): void
    {
        $member = $this->makeCommitteeMember();
        $this->post(route('admin.events.panitia.payroll.store', [$this->event->id, $member->id]), [
            'base_salary' => 500000,
        ]);
        $payroll = $member->payrolls()->first();

        $payResponse = $this->post(route('admin.events.panitia.payroll.pay', [$this->event->id, $member->id, $payroll->id]));
        $payResponse->assertRedirect();
        $this->assertSame('paid', $payroll->fresh()->status);

        $unpayResponse = $this->post(route('admin.events.panitia.payroll.pay', [$this->event->id, $member->id, $payroll->id]));
        $unpayResponse->assertRedirect();
        $this->assertSame('draft', $payroll->fresh()->status);
    }

    public function test_admin_can_remove_committee_member(): void
    {
        $member = $this->makeCommitteeMember();

        $response = $this->delete(route('admin.events.panitia.destroy', [$this->event->id, $member->id]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('event_committees', ['id' => $member->id]);
    }

    public function test_export_panitia_downloads_csv(): void
    {
        $member = $this->makeCommitteeMember();
        $member->user->update(['phone' => '081234567890']);
        $this->post(route('admin.events.panitia.payroll.store', [$this->event->id, $member->id]), [
            'base_salary' => 1000000,
            'allowance' => 100000,
        ]);

        $response = $this->get(route('admin.events.export', [$this->event, 'panitia']));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('full_name,email,phone,role,status,joined_at,base_salary,allowance,overtime,deduction', $content);
        $this->assertStringContainsString($member->user->email, $content);
        $this->assertStringContainsString('1000000', $content);
    }

    public function test_import_panitia_creates_committee_and_payroll(): void
    {
        $csv = implode("\n", [
            'full_name,email,phone,role,status,joined_at,base_salary,allowance,overtime,deduction',
            'Sinta Delegasi,sinta@panitia.test,081111111111,sekretaris,aktif,2026-11-01,800000,100000,0,0',
            'Budi Bendahara,budi@panitia.test,082222222222,bendahara,aktif,2026-11-02,900000,0,50000,100000',
        ]);

        $response = $this->post(route('admin.events.import', [$this->event, 'panitia']), [
            'file' => UploadedFile::fake()->createWithContent('panitia.csv', $csv),
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['email' => 'sinta@panitia.test', 'name' => 'Sinta Delegasi']);
        $this->assertDatabaseHas('event_committees', ['role' => 'sekretaris']);
        $this->assertDatabaseHas('event_committees', ['role' => 'bendahara']);

        $sinta = User::where('email', 'sinta@panitia.test')->firstOrFail();
        $payroll = $sinta->committees()->where('event_id', $this->event->id)->firstOrFail()->payrolls()->first();
        $this->assertSame('900000.00', (string) $payroll->net_salary);
    }

    public function test_import_panitia_updates_existing_by_email(): void
    {
        $existing = User::factory()->create(['email' => 'imported@panitia.test', 'name' => 'Nama Lama']);

        $csv = implode("\n", [
            'full_name,email,phone,role,status,joined_at,base_salary,allowance,overtime,deduction',
            'Nama Baru,imported@panitia.test,089999999999,koordinator,aktif,2026-11-03,750000,0,0,0',
        ]);

        $response = $this->post(route('admin.events.import', [$this->event, 'panitia']), [
            'file' => UploadedFile::fake()->createWithContent('panitia.csv', $csv),
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('Nama Baru', $existing->fresh()->name);
        $this->assertDatabaseHas('event_committees', ['user_id' => $existing->id, 'role' => 'koordinator']);
    }

    public function test_import_panitia_rejects_invalid_role(): void
    {
        $csv = implode("\n", [
            'full_name,email,phone,role,status,joined_at,base_salary,allowance,overtime,deduction',
            'Gagal Gaji,gagal@panitia.test,081111,counsellor,aktif,2026-11-01,800000,0,0,0',
        ]);

        $response = $this->from(route('admin.events.panitia.index', $this->event))
            ->post(route('admin.events.import', [$this->event, 'panitia']), [
                'file' => UploadedFile::fake()->createWithContent('panitia.csv', $csv),
            ]);

        $response->assertSessionHasErrors('import');
        $this->assertSame(0, $this->event->committees()->count());
    }

    public function test_id_card_page_renders_for_committee_member(): void
    {
        $member = $this->makeCommitteeMember();

        $response = $this->get(route('admin.events.panitia.id-card', [$this->event->id, $member->id]));

        $response->assertOk();
        $response->assertSee($member->user->name);
        $response->assertSee('PANITIA');
        $response->assertSee('window.print()', false);
    }

    private function makeCommitteeMember(): EventCommittee
    {
        $user = User::factory()->create();

        return EventCommittee::create([
            'event_id' => $this->event->id,
            'user_id' => $user->id,
            'role' => 'staff',
            'status' => 'aktif',
            'joined_at' => now()->toDateString(),
        ]);
    }
}
