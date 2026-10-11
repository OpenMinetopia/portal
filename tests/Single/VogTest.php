<?php

use App\Demo\DemoWorld;
use App\Demo\FakeMojangApiService;
use App\Demo\FakePluginApiService;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\MojangApiService;
use App\Services\Plugin\PluginApiService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

const VOG_BASE = 'http://portaal.test';

/** Answers like a plugin that knows VOGs, and remembers what the portal asked it. */
class VogPlugin extends FakePluginApiService
{
    public array $posts = [];

    public ?array $answer = null;

    public bool $knowsVogs = true;

    public ?array $vogs = null;

    public bool $automatic = true;

    public function get(string $endpoint, array $query = []): mixed
    {
        if (str_ends_with($endpoint, '/vogs') && ! $this->knowsVogs) {
            return null;
        }

        if (str_ends_with($endpoint, '/vogs') && ($this->vogs !== null || ! $this->automatic)) {
            return ['success' => true, 'automatic' => $this->automatic, 'vogs' => $this->vogs ?? []];
        }

        return parent::get($endpoint, $query);
    }

    public function postOrError(string $endpoint, array $data = []): ?array
    {
        $this->posts[] = [$endpoint, $data];

        return $this->answer ?? parent::postOrError($endpoint, $data);
    }
}

beforeEach(function () {
    Http::preventStrayRequests();
    $this->travelTo(now()->setDate(2026, 10, 1)->setTime(12, 0));

    $this->plugin = new VogPlugin();
    $this->app->instance(PluginApiService::class, $this->plugin);
    $this->app->bind(MojangApiService::class, FakeMojangApiService::class);

    $police = Role::create(['name' => 'Politie', 'slug' => 'politie']);
    $police->permissions()->sync(Permission::where('slug', 'manage-police')->pluck('id'));

    $this->agent = vogUser('Agent_Sanne');
    $this->agent->roles()->attach($police);
    $this->daan = vogUser('Daan');
    $this->lotte = vogUser('Lotte');
});

function vogUser(string $name): User
{
    $uuid = DemoWorld::PLAYERS[$name];

    return User::create([
        'name' => $name,
        'email' => Str::lower($name).'@example.com',
        'minecraft_username' => $name,
        'minecraft_uuid' => str_replace('-', '', $uuid),
        'minecraft_plain_uuid' => $uuid,
        'password' => Hash::make('geheim123'),
        'token' => Str::random(32),
        'minecraft_verified' => true,
    ]);
}

it('asks the plugin to issue the VOG for the agent', function () {
    $this->actingAs($this->agent)
        ->post(VOG_BASE.'/portal/police/players/'.$this->daan->id.'/vog')
        ->assertSessionHas('success', 'VOG voor Daan is negatief. Het boek staat in je inventory.');

    expect($this->plugin->posts)->toBe([
        ['/api/player/'.DemoWorld::PLAYERS['Daan'].'/vogs', ['officer' => DemoWorld::PLAYERS['Agent_Sanne']]],
    ]);
});

it('says why the plugin refused', function () {
    $this->plugin->answer = ['success' => false, 'code' => 'officer_offline', 'error' => 'The officer must be online.'];

    $this->actingAs($this->agent)
        ->post(VOG_BASE.'/portal/police/players/'.$this->lotte->id.'/vog')
        ->assertSessionHas('error', fn ($error) => str_contains($error['message'], 'online zijn op de server'));
});

it('lets only agents issue a VOG', function () {
    $this->actingAs($this->lotte)
        ->post(VOG_BASE.'/portal/police/players/'.$this->daan->id.'/vog')
        ->assertForbidden();

    expect($this->plugin->posts)->toBe([]);
});

it('shows the plugin VOGs to the agent and to the player', function () {
    $this->actingAs($this->agent)->get(VOG_BASE.'/portal/police/players/'.$this->lotte->id)
        ->assertOk()->assertSee('VOG afgeven')->assertSee('Positief')->assertSee('Geldig tot 06-10-2026');

    $this->actingAs($this->lotte)->get(VOG_BASE.'/portal/criminal-records')
        ->assertOk()->assertSee('Mijn VOG')->assertSee('Positief');
});

it('marks a VOG older than a week as expired', function () {
    $given = now()->subDays(10);
    $this->plugin->vogs = [[
        'id' => 3, 'positive' => false, 'record_count' => 2, 'officer' => DemoWorld::PLAYERS['Agent_Sanne'],
        'date' => $given->getTimestampMs(), 'valid_until' => $given->copy()->addWeek()->getTimestampMs(),
    ]];

    $this->actingAs($this->daan)->get(VOG_BASE.'/portal/criminal-records')
        ->assertOk()->assertSee('Negatief')->assertSee('Verlopen')->assertSee('2 overtredingen')->assertSee('Agent: Agent_Sanne');
});

it('hides the button when the plugin does not know VOGs yet', function () {
    $this->plugin->knowsVogs = false;

    $this->actingAs($this->agent)->get(VOG_BASE.'/portal/police/players/'.$this->lotte->id)
        ->assertOk()->assertDontSee('VOG afgeven')->assertSee('/boek krijg vog');

    $this->actingAs($this->lotte)->get(VOG_BASE.'/portal/criminal-records')
        ->assertOk()->assertDontSee('Mijn VOG');
});

it('leaves VOGs to the agent in-game when the server fills them in by hand', function () {
    $this->plugin->automatic = false;

    $this->actingAs($this->agent)->get(VOG_BASE.'/portal/police/players/'.$this->lotte->id)
        ->assertOk()->assertDontSee('VOG afgeven')->assertSee('vullen agenten de uitslag zelf in');

    $this->plugin->answer = ['success' => false, 'code' => 'vog_manual', 'error' => 'VOGs are filled in by hand.'];
    $this->post(VOG_BASE.'/portal/police/players/'.$this->lotte->id.'/vog')
        ->assertSessionHas('error', fn ($error) => str_contains($error['message'], 'met de hand'));
});
