<?php

use App\Models\AuditLog;
use App\Models\Location;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'web']);
});

test('unauthenticated guest is redirected to login when accessing audit logs', function () {
    $response = $this->get(route('admin.audit-logs.index'));
    $response->assertRedirect('/');
});

test('regular employee without superadmin role is forbidden from audit logs', function () {
    $employee = User::factory()->create();

    $response = $this->actingAs($employee)->get(route('admin.audit-logs.index'));
    // Redirected from /admin prefix because not in admin|HR|super-admin, or 403
    expect($response->status())->toBeIn([302, 403]);
});

test('admin or HR role without superadmin role cannot access audit logs (403 forbidden)', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get(route('admin.audit-logs.index'));
    $response->assertStatus(403);

    $hr = User::factory()->create();
    $hr->assignRole('HR');

    $responseHr = $this->actingAs($hr)->get(route('admin.audit-logs.index'));
    $responseHr->assertStatus(403);
});

test('superadmin can access audit logs page and view records', function () {
    $superadmin = User::factory()->create();
    $superadmin->assignRole('super-admin');

    // Create a dummy audit log
    $location = Location::create([
        'name' => 'Kantor Cabang Jakarta',
        'latitude' => -6.200000,
        'longitude' => 106.816666,
        'radius' => 50,
    ]);

    $response = $this->actingAs($superadmin)->get(route('admin.audit-logs.index'));
    $response->assertOk();
    $response->assertSee('Log Audit CRUD');
    $response->assertSee('Kantor Cabang Jakarta');
});

test('superadmin can filter audit logs by event and model', function () {
    $superadmin = User::factory()->create();
    $superadmin->assignRole('super-admin');

    $location = Location::create([
        'name' => 'Kantor Medan',
        'latitude' => 3.595196,
        'longitude' => 98.672226,
        'radius' => 60,
    ]);

    $response = $this->actingAs($superadmin)->get(route('admin.audit-logs.index', [
        'event' => 'created',
        'model' => Location::class,
    ]));

    $response->assertOk();
    $response->assertSee('Kantor Medan');
});

test('superadmin can fetch log detail via show endpoint', function () {
    $superadmin = User::factory()->create();
    $superadmin->assignRole('super-admin');

    $location = Location::create([
        'name' => 'Kantor Surabaya',
        'latitude' => -7.257472,
        'longitude' => 112.752090,
        'radius' => 80,
    ]);

    $log = AuditLog::where('auditable_type', Location::class)
        ->where('auditable_id', $location->id)
        ->first();

    $response = $this->actingAs($superadmin)->getJson(route('admin.audit-logs.show', $log));
    $response->assertOk();
    $response->assertJsonPath('event', 'created');
    $response->assertJsonPath('model_label', 'Lokasi Kerja (GPS)');
    $response->assertJsonPath('after.name', 'Kantor Surabaya');
});

test('superadmin can export audit logs to excel', function () {
    $superadmin = User::factory()->create();
    $superadmin->assignRole('super-admin');

    Location::create([
        'name' => 'Kantor Denpasar',
        'latitude' => -8.670458,
        'longitude' => 115.212629,
        'radius' => 50,
    ]);

    $response = $this->actingAs($superadmin)->get(route('admin.audit-logs.export'));
    $response->assertOk();
    $response->assertHeader('content-disposition');
    expect($response->headers->get('content-disposition'))->toContain('.xlsx');
});

test('non-superadmin cannot export audit logs to excel', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get(route('admin.audit-logs.export'));
    $response->assertStatus(403);
});
