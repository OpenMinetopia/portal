<?php

use App\Models\BrandingSetting;
use App\Models\Role;
use App\Models\User;
use App\Support\Theme;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    Http::fake();

    $this->admin = User::create([
        'name' => 'Beheerder', 'email' => 'beheerder@example.com', 'minecraft_username' => 'Beheerder',
        'password' => Hash::make('geheim123'), 'token' => Str::random(32),
        'minecraft_verified' => true, 'minecraft_uuid' => (string) Str::uuid(), 'minecraft_plain_uuid' => (string) Str::uuid(),
    ]);
    $this->admin->roles()->attach(Role::where('is_admin', true)->firstOrFail());
});

function themeFile(array $data): UploadedFile
{
    return UploadedFile::fake()->createWithContent('thema.json', json_encode($data));
}

it('ships only valid themes in the gallery', function () {
    $gallery = Theme::gallery();

    expect($gallery)->not->toBeEmpty()
        ->and($gallery->get('openminetopia')->accent)->toBe(BrandingSetting::DEFAULT_ACCENT);
});

it('leaves the default look alone until an admin picks a theme', function () {
    $this->actingAs($this->admin)->get('http://portaal.test/portal/admin/appearance')
        ->assertOk()->assertSee('Diamant')->assertDontSee('--gray-50:', false);
});

it('saves a theme and renders it as css variables', function () {
    $this->actingAs($this->admin)->put('http://portaal.test/portal/admin/appearance', [
        'accent_color' => '#0E7490', 'neutrals' => 'cool', 'font' => 'inter', 'heading_font' => 'monocraft', 'radius' => 'large',
    ])->assertSessionHasNoErrors();

    expect(BrandingSetting::first())
        ->accent_color->toBe('#0e7490')
        ->neutrals->toBe('cool')
        ->font->toBe('inter')
        ->heading_font->toBe('monocraft')
        ->radius->toBe('large');

    $this->get('http://portaal.test/portal/admin/appearance')
        ->assertSee('--gray-50: 248 250 252;', false)
        ->assertSee('--font-sans: "Inter";', false)
        ->assertSee('--font-heading: "Monocraft";', false)
        ->assertSee('--radius-lg: 14px;', false)
        ->assertSee('family=Inter:wght@400..800', false);
});

it('refuses fonts, neutrals and corners it does not know', function () {
    $this->actingAs($this->admin)->put('http://portaal.test/portal/admin/appearance', [
        'accent_color' => '#3f6b30', 'neutrals' => 'pink', 'font' => 'Comic Sans"; } body { display: none', 'heading_font' => 'papyrus', 'radius' => 'huge',
    ])->assertSessionHasErrors(['neutrals', 'font', 'heading_font', 'radius']);

    expect(BrandingSetting::count())->toBe(0);
});

it('exports the saved theme without the logo', function () {
    BrandingSetting::create(['accent_color' => '#b91c1c', 'neutrals' => 'neutral', 'font' => 'space-grotesk', 'radius' => 'small', 'logo_path' => 'branding/logo.png']);

    $json = $this->actingAs($this->admin)->get('http://portaal.test/portal/admin/appearance/export')
        ->assertOk()->assertDownload('test-portaal-thema.json')->getContent();

    expect(json_decode($json, true))->toBe([
        'format' => 'openminetopia-theme', 'version' => 1, 'name' => config('app.name'),
        'accent' => '#b91c1c', 'neutrals' => 'neutral', 'font' => 'space-grotesk', 'radius' => 'small',
    ]);
});

it('loads an imported theme into the form without saving it', function () {
    $exported = Theme::gallery()->get('overworld')->toArray();

    $this->actingAs($this->admin)
        ->from('http://portaal.test/portal/admin/appearance')
        ->post('http://portaal.test/portal/admin/appearance/import', ['theme' => themeFile($exported)])
        ->assertRedirect('http://portaal.test/portal/admin/appearance')
        ->assertSessionHasInput('font', 'schibsted')
        ->assertSessionHasInput('heading_font', 'monocraft')
        ->assertSessionHasInput('accent_color', '#a16207');

    expect(BrandingSetting::count())->toBe(0);
});

it('rejects a theme file that is not a theme', function (array $data, string $message) {
    $this->actingAs($this->admin)
        ->post('http://portaal.test/portal/admin/appearance/import', ['theme' => themeFile($data)])
        ->assertSessionHasErrors(['theme' => $message]);
})->with([
    'any json' => [['hello' => 'world'], 'Dit is geen themabestand van het portaal.'],
    'unknown font' => [['format' => 'openminetopia-theme', 'version' => 1, 'name' => 'X', 'accent' => '#000000', 'font' => 'https://evil.example/font.css'], 'Het thema gebruikt een lettertype dat dit portaal niet kent.'],
    'newer version' => [['format' => 'openminetopia-theme', 'version' => 2, 'name' => 'X', 'accent' => '#000000'], 'Dit thema is gemaakt voor een nieuwere versie van het portaal.'],
]);

it('fills in defaults for a theme that leaves tokens out', function () {
    $theme = Theme::fromArray(['format' => 'openminetopia-theme', 'version' => 1, 'name' => 'Alleen kleur', 'accent' => '#7C3AED']);

    expect($theme->formInput())->toBe([
        'accent_color' => '#7c3aed', 'neutrals' => 'warm', 'font' => 'schibsted', 'heading_font' => '', 'radius' => 'default',
    ]);
});

it('does not accept a theme without a name', function () {
    Theme::fromArray(['format' => 'openminetopia-theme', 'version' => 1, 'accent' => '#7c3aed']);
})->throws(ValidationException::class);
