<?php

use App\Models\Tenant;
use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->alpha = $this->createTenant('alpha');
    $this->beta = $this->createTenant('beta');

    Notification::fake();
});

function resetUser(Tenant $tenant, string $password = 'oud-wachtwoord'): User
{
    return $tenant->run(fn () => User::create([
        'name' => 'Speler', 'email' => 'speler@example.com', 'minecraft_username' => 'Speler',
        'password' => Hash::make($password), 'token' => Str::random(32),
    ]));
}

/** Requests a reset link on the tenant and returns the token from the notification. */
function requestResetToken(string $host, User $user): string
{
    test()->post("http://{$host}/wachtwoord-vergeten", ['email' => $user->email])
        ->assertSessionHas('status');

    $token = null;
    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token) {
        $token = $notification->token;

        return true;
    });

    return $token;
}

function passwordMatches(Tenant $tenant, string $password): bool
{
    return $tenant->run(fn () => Hash::check($password, User::where('email', 'speler@example.com')->value('password')));
}

it('shows a forgot password link on the login page', function () {
    $this->get($this->tenantUrl('alpha', '/login'))
        ->assertOk()
        ->assertSee($this->tenantUrl('alpha', '/wachtwoord-vergeten'));
});

it('mails a dutch link on the tenant domain', function () {
    $user = resetUser($this->alpha);
    $token = requestResetToken('alpha.mtportal.test', $user);

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user, $token) {
        $mail = $notification->toMail($user);

        return $notification->locale === 'nl'
            && $mail->subject === 'Nieuw wachtwoord instellen'
            && $mail->actionUrl === $this->tenantUrl('alpha', "/wachtwoord-herstellen/{$token}?email=speler%40example.com");
    });
});

it('gives the same answer for an unknown address', function () {
    $this->post($this->tenantUrl('alpha', '/wachtwoord-vergeten'), ['email' => 'niemand@example.com'])
        ->assertSessionHas('status')
        ->assertSessionHasNoErrors();

    Notification::assertNothingSent();
});

it('sets the new password and logs out other sessions', function () {
    $user = resetUser($this->alpha);
    $token = requestResetToken('alpha.mtportal.test', $user);

    $this->alpha->run(fn () => DB::table('sessions')->insert([
        'id' => 'andere-sessie', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time(),
    ]));

    $this->get($this->tenantUrl('alpha', "/wachtwoord-herstellen/{$token}?email=speler%40example.com"))
        ->assertOk()
        ->assertSee('value="speler@example.com"', false);

    $this->post($this->tenantUrl('alpha', '/wachtwoord-herstellen'), [
        'token' => $token,
        'email' => 'speler@example.com',
        'password' => 'nieuw-wachtwoord',
        'password_confirmation' => 'nieuw-wachtwoord',
    ])->assertRedirect($this->tenantUrl('alpha', '/login'))->assertSessionHas('status');

    expect(passwordMatches($this->alpha, 'nieuw-wachtwoord'))->toBeTrue()
        ->and($this->alpha->run(fn () => DB::table('sessions')->where('user_id', $user->id)->exists()))->toBeFalse();

    $this->post($this->tenantUrl('alpha', '/login'), ['email' => 'speler@example.com', 'password' => 'nieuw-wachtwoord'])
        ->assertRedirect();
    $this->assertAuthenticated();
});

it('rejects a wrong token', function () {
    resetUser($this->alpha);

    $this->post($this->tenantUrl('alpha', '/wachtwoord-herstellen'), [
        'token' => 'verzonnen',
        'email' => 'speler@example.com',
        'password' => 'nieuw-wachtwoord',
        'password_confirmation' => 'nieuw-wachtwoord',
    ])->assertSessionHasErrors('email');

    expect(passwordMatches($this->alpha, 'oud-wachtwoord'))->toBeTrue();
});

it('does not accept a token from another portal', function () {
    $user = resetUser($this->alpha);
    resetUser($this->beta);
    $token = requestResetToken('alpha.mtportal.test', $user);

    $this->post($this->tenantUrl('beta', '/wachtwoord-herstellen'), [
        'token' => $token,
        'email' => 'speler@example.com',
        'password' => 'nieuw-wachtwoord',
        'password_confirmation' => 'nieuw-wachtwoord',
    ])->assertSessionHasErrors('email');

    expect(passwordMatches($this->beta, 'oud-wachtwoord'))->toBeTrue()
        ->and(passwordMatches($this->alpha, 'oud-wachtwoord'))->toBeTrue();
});
