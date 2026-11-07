<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventTicketTier;
use App\Models\Registrant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MasterDataImportExportTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = Event::factory()->create();
    }

    public function test_export_tiers_downloads_csv_with_header_and_rows(): void
    {
        $tier = EventTicketTier::factory()->create([
            'event_id' => $this->event->id,
            'code' => 'REG',
            'name' => 'Regular',
        ]);

        $response = $this->get(route('admin.events.export', [$this->event, 'tiers']));

        $response->assertOk();
        $response->assertDownload($this->event->slug.'-tiers.csv');

        $body = $response->streamedContent();

        $this->assertStringContainsString('name,code,slug,price', $body);
        $this->assertStringContainsString('Regular,REG,', $body);
        $this->assertStringContainsString($tier->price, $body);
    }

    public function test_export_registrants_includes_tier_code(): void
    {
        $tier = EventTicketTier::factory()->create([
            'event_id' => $this->event->id,
            'code' => 'VIP',
        ]);

        Registrant::factory()->create([
            'event_id' => $this->event->id,
            'ticket_tier_id' => $tier->id,
            'full_name' => 'Sari Wijaya',
            'email' => 'sari@example.test',
        ]);

        $response = $this->get(route('admin.events.export', [$this->event, 'registrants']));

        $response->assertOk();
        $content = $response->streamedContent();
        // fputcsv mengutip field yang berisi spasi, jadi cocokkan per bagian.
        $this->assertStringContainsString('sari@example.test', $content);
        $this->assertStringContainsString('VIP', $content);
        $this->assertStringContainsString('Sari', $content);
    }

    public function test_template_download_contains_sample_row(): void
    {
        $response = $this->get(route('admin.events.export.template', [$this->event, 'vouchers']));

        $response->assertOk();
        $response->assertDownload('template-vouchers.csv');
        $this->assertStringContainsString('code,value,is_percent', $response->streamedContent());
        $this->assertStringContainsString('HEMAT10', $response->streamedContent());
    }

    public function test_invalid_export_type_returns_404(): void
    {
        $this->get(route('admin.events.export', [$this->event, 'apapun']))->assertNotFound();
        $this->post(route('admin.events.import', [$this->event, 'apapun']))->assertNotFound();
    }

    public function test_import_tiers_creates_new_rows(): void
    {
        $csv = implode("\n", [
            'name,code,slug,price,fee,description,stock_qty,seat_qty,sold_qty,is_downloadable,is_show_in_list,is_selectable_on_site,is_selectable_online',
            'Regular,REG,regular,150000,5000,Tiket umum,100,0,0,0,1,1,1',
            'VIP,VIP,vip,350000,0,Tiket VIP,20,0,0,1,1,1,1',
        ]);

        $response = $this->from(route('admin.events.tiers.index', $this->event))
            ->post(route('admin.events.import', [$this->event, 'tiers']), [
                'file' => $this->csvFile($csv),
            ]);

        $response->assertSessionHas('success');
        $this->assertSame(2, $this->event->tiers()->count());

        $vip = $this->event->tiers()->where('code', 'VIP')->firstOrFail();
        $this->assertSame('350000.00', (string) $vip->price);
        $this->assertSame(20, $vip->stock_qty);
        $this->assertTrue($vip->is_downloadable);
    }

    public function test_import_updates_existing_tier_by_code(): void
    {
        EventTicketTier::factory()->create([
            'event_id' => $this->event->id,
            'code' => 'REG',
            'name' => 'Regular Lama',
            'price' => '100000.00',
        ]);

        $csv = implode("\n", [
            'name,code,slug,price',
            'Regular Baru,REG,regular-baru,175000',
        ]);

        $this->post(route('admin.events.import', [$this->event, 'tiers']), [
            'file' => $this->csvFile($csv),
        ]);

        $this->assertSame(1, $this->event->tiers()->count());

        $tier = $this->event->tiers()->where('code', 'REG')->firstOrFail();
        $this->assertSame('Regular Baru', $tier->name);
        $this->assertSame('175000.00', (string) $tier->price);
    }

    public function test_import_vouchers_parses_booleans_and_dates(): void
    {
        $csv = implode("\n", [
            'code,value,is_percent,is_single_use,is_active,total_uses,uses,expired_at,order_by',
            'HEMAT10,10,yes,0,true,100,0,2026-12-31 23:59:59,1',
        ]);

        $response = $this->post(route('admin.events.import', [$this->event, 'vouchers']), [
            'file' => $this->csvFile($csv),
        ]);

        $response->assertSessionHas('success');

        $voucher = $this->event->vouchers()->where('code', 'HEMAT10')->firstOrFail();
        $this->assertTrue($voucher->is_percent);
        $this->assertFalse($voucher->is_single_use);
        $this->assertTrue($voucher->is_active);
        $this->assertSame('2026-12-31 23:59:59', $voucher->expired_at->format('Y-m-d H:i:s'));
    }

    public function test_import_addons_creates_rows(): void
    {
        $csv = implode("\n", [
            'name,code,slug,price,fee,description,qty,max_qty,sold_qty,order_by,is_selectable_on_site,is_selectable_online',
            'Merch Kaos,MERCH,merch-kaos,75000,0,Kaos,50,5,0,1,1,1',
        ]);

        $response = $this->post(route('admin.events.import', [$this->event, 'addons']), [
            'file' => $this->csvFile($csv),
        ]);

        $response->assertSessionHas('success');
        $this->assertSame(1, EventAddon::count());

        $addon = $this->event->addons()->where('code', 'MERCH')->firstOrFail();
        $this->assertSame(50, $addon->qty);
        $this->assertTrue($addon->is_selectable_online);
    }

    public function test_import_registrants_creates_registrant_with_ticket(): void
    {
        $tier = EventTicketTier::factory()->create([
            'event_id' => $this->event->id,
            'code' => 'REG',
            'price' => '150000.00',
        ]);

        $csv = implode("\n", [
            'full_name,email,phone,status,ticket_tier,city,address,notes,subtotal,addons_total,discount,total,fee,is_paid,participant_token',
            'Budi Santoso,budi@example.test,08123456789,confirmed,REG,Bandung,Jl. Merdeka 1,,150000,0,0,,5000,1,',
        ]);

        $response = $this->post(route('admin.events.import', [$this->event, 'registrants']), [
            'file' => $this->csvFile($csv),
        ]);

        $response->assertSessionHas('success');

        $registrant = $this->event->registrants()->where('email', 'budi@example.test')->firstOrFail();

        $this->assertSame('Budi Santoso', $registrant->full_name);
        $this->assertSame('confirmed', $registrant->status);
        $this->assertSame($tier->id, $registrant->ticket_tier_id);
        // Kolom total kosong → dihitung dari subtotal - diskon.
        $this->assertSame('150000.00', (string) $registrant->total);
        $this->assertTrue($registrant->is_paid);
        $this->assertNotNull($registrant->participant_token);
        $this->assertNotNull($registrant->paid_at);
        $this->assertSame(1, $registrant->ticketItems()->count());
    }

    public function test_import_with_unknown_tier_code_shows_row_error_and_creates_nothing(): void
    {
        $csv = implode("\n", [
            'full_name,email,status,ticket_tier',
            'Andi,andi@example.test,confirmed,TIER-TIDAK-ADA',
        ]);

        $response = $this->from(route('admin.events.registrants.index', $this->event))
            ->post(route('admin.events.import', [$this->event, 'registrants']), [
                'file' => $this->csvFile($csv),
            ]);

        $response->assertSessionHasErrors('import');
        $this->assertSame(0, $this->event->registrants()->count());
    }

    public function test_import_rejects_file_with_missing_columns(): void
    {
        $csv = "name,price\nRegular,150000\n";

        $response = $this->from(route('admin.events.tiers.index', $this->event))
            ->post(route('admin.events.import', [$this->event, 'tiers']), [
                'file' => $this->csvFile($csv),
            ]);

        $response->assertSessionHasErrors('import');
        $this->assertSame(0, $this->event->tiers()->count());
    }

    public function test_import_reports_validation_errors_with_line_numbers(): void
    {
        $csv = implode("\n", [
            'name,code,price',
            ',NO-NAME,150000',
        ]);

        $response = $this->from(route('admin.events.tiers.index', $this->event))
            ->post(route('admin.events.import', [$this->event, 'tiers']), [
                'file' => $this->csvFile($csv),
            ]);

        $response->assertSessionHasErrors('import');
        $this->assertSame(0, $this->event->tiers()->count());
    }

    public function test_import_requires_a_file(): void
    {
        $response = $this->post(route('admin.events.import', [$this->event, 'tiers']));

        $response->assertSessionHasErrors('file');
    }

    public function test_import_handles_semicolon_delimiter_from_excel(): void
    {
        $csv = implode("\n", [
            'name;code;price',
            'Regular;REG2;150000',
        ]);

        $response = $this->post(route('admin.events.import', [$this->event, 'tiers']), [
            'file' => $this->csvFile($csv),
        ]);

        $response->assertSessionHas('success');
        $this->assertSame(1, $this->event->tiers()->count());
    }

    public function test_all_four_index_pages_show_import_export_buttons(): void
    {
        foreach (['tiers', 'addons', 'vouchers', 'registrants'] as $type) {
            $response = $this->get(route('admin.events.'.$type.'.index', $this->event));

            $response->assertOk();
            $response->assertSee(route('admin.events.export', [$this->event, $type]), false);
            $response->assertSee(route('admin.events.export.template', [$this->event, $type]), false);
            $response->assertSee(route('admin.events.import', [$this->event, $type]), false);
        }
    }

    private function csvFile(string $content, string $name = 'import.csv'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, $content);
    }
}
