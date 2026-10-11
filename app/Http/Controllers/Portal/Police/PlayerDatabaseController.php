<?php

namespace App\Http\Controllers\Portal\Police;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Plugin\VogService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PlayerDatabaseController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('minecraft_username', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('minecraft_username');

        if ($request->filled('has_records')) {
            // Get all users and filter them
            $users = $query->get()->filter(function ($user) use ($request) {
                $hasRecords = count($user->criminal_records) > 0;
                return $request->has_records === '1' ? $hasRecords : !$hasRecords;
            });

            // Manual pagination for filtered results
            $page = $request->get('page', 1);
            $perPage = 25;
            $items = $users->sortBy('minecraft_username')
                ->forPage($page, $perPage);

            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $users->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            $users = $paginator;
        } else {
            // If no filter is applied, use regular pagination
            $users = $query->paginate(25)->withQueryString();
        }

        if ($request->ajax()) {
            return view('portal.police.players.partials.table', compact('users'));
        }

        return view('portal.police.players.index', compact('users'));
    }

    public function show(User $user, VogService $vogs)
    {
        return view('portal.police.players.show', [
            'user' => $user,
            'records' => $user->criminal_records,
            'vogs' => $user->minecraft_plain_uuid ? $vogs->forPlayer($user->minecraft_plain_uuid) : null,
        ]);
    }

    /** The plugin issues the VOG and gives the agent the book in-game. */
    public function issueVog(User $user, VogService $vogs)
    {
        $officer = auth()->user();

        if (! $user->minecraft_plain_uuid || ! $officer->minecraft_plain_uuid) {
            return back()->with('error', ['title' => 'Geen VOG afgegeven', 'message' => 'Een Minecraft-account ontbreekt.']);
        }

        $result = $vogs->issue($user->minecraft_plain_uuid, $officer->minecraft_plain_uuid);

        if (isset($result['error'])) {
            return back()->with('error', ['title' => 'Geen VOG afgegeven', 'message' => $result['error']]);
        }

        return back()->with('success', sprintf(
            'VOG voor %s is %s. Het boek staat in je inventory.',
            $user->minecraft_username,
            $result['vog']['positive'] ? 'positief' : 'negatief',
        ));
    }
} 