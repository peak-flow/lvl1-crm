<?php

use App\Livewire\Admin\EditMembership;
use App\Livewire\Members\WaiverForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('sends guests to the sign-in page', function () {
    $this->get('/waiver')->assertRedirect(route('login'));
});

it('saves the emergency contact and records when the waiver was signed', function () {
    $member = User::factory()->withoutWaiver()->create();

    Livewire::actingAs($member)
        ->test(WaiverForm::class)
        ->set('emergencyContactName', 'Charles Babbage')
        ->set('emergencyContactPhone', '(502) 555-0142')
        ->set('agreesToWaiver', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $member->refresh();

    expect($member->emergency_contact_name)->toBe('Charles Babbage');
    expect($member->emergency_contact_phone)->toBe('(502) 555-0142');
    expect($member->hasSignedWaiver())->toBeTrue();
});

it('does not save anything unless the member agrees to the waiver', function () {
    $member = User::factory()->withoutWaiver()->create();

    Livewire::actingAs($member)
        ->test(WaiverForm::class)
        ->set('emergencyContactName', 'Charles Babbage')
        ->set('emergencyContactPhone', '502-555-0142')
        ->call('save')
        ->assertHasErrors(['agreesToWaiver']);

    $member->refresh();

    expect($member->hasSignedWaiver())->toBeFalse();
    expect($member->emergency_contact_name)->toBeNull();
});

it('rejects a missing contact name and a phone number that is not one', function (string $name, string $phone, string $fieldWithError) {
    Livewire::actingAs(User::factory()->withoutWaiver()->create())
        ->test(WaiverForm::class)
        ->set('emergencyContactName', $name)
        ->set('emergencyContactPhone', $phone)
        ->set('agreesToWaiver', true)
        ->call('save')
        ->assertHasErrors([$fieldWithError]);
})->with([
    'no name' => ['', '502-555-0142', 'emergencyContactName'],
    'no phone' => ['Charles Babbage', '', 'emergencyContactPhone'],
    'letters in phone' => ['Charles Babbage', 'call my mum', 'emergencyContactPhone'],
    'phone too short' => ['Charles Babbage', '12345', 'emergencyContactPhone'],
]);

it('lets a member who already signed change their contact without agreeing again', function () {
    $member = User::factory()->create();
    $originalSignedAt = $member->waiver_signed_at;

    $this->travel(3)->days();

    Livewire::actingAs($member)
        ->test(WaiverForm::class)
        ->assertSet('emergencyContactName', $member->emergency_contact_name)
        ->set('emergencyContactName', 'Mary Somerville')
        ->call('save')
        ->assertHasNoErrors();

    $member->refresh();

    expect($member->emergency_contact_name)->toBe('Mary Somerville');
    expect($member->waiver_signed_at->equalTo($originalSignedAt))->toBeTrue();
});

it('keeps the ideas board closed until the waiver is signed', function () {
    $member = User::factory()->withoutWaiver()->create();

    $this->actingAs($member)->get('/ideas')->assertRedirect(route('waiver.edit'));

    $member->waiver_signed_at = now();
    $member->save();

    $this->actingAs($member)->get('/ideas')->assertOk();
});

it('prompts a member on the dashboard until the waiver is signed', function () {
    $this->actingAs(User::factory()->withoutWaiver()->create())
        ->get('/dashboard')
        ->assertSee('One more step before you use the space');

    $this->actingAs(User::factory()->create(['emergency_contact_name' => 'Charles Babbage']))
        ->get('/dashboard')
        ->assertDontSee('One more step before you use the space')
        ->assertSee('Charles Babbage');
});

it('shows an admin the member\'s emergency contact and waiver state', function () {
    $signedMember = User::factory()->create(['emergency_contact_name' => 'Charles Babbage']);
    $unsignedMember = User::factory()->withoutWaiver()->create();
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)->test(EditMembership::class, ['member' => $signedMember])->assertSee('Charles Babbage');
    Livewire::actingAs($admin)->test(EditMembership::class, ['member' => $unsignedMember])->assertSee(['Not given yet', 'Not signed']);
});
