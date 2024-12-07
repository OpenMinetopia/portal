<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MojangApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MinecraftApiController extends Controller
{
    protected $mojangApi;

    public function __construct(MojangApiService $mojangApi)
    {
        $this->mojangApi = $mojangApi;
    }

    public function getPlayer(string $username)
    {
        try {
            $playerData = $this->mojangApi->getPlayerData($username);

            if ($playerData) {
                return response()->json([
                    'success' => true,
                    'uuid' => $playerData['uuid'],
                    'name' => $playerData['name'],
                    'skin_url' => $playerData['skin_url']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Player not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching player data', [
                'username' => $username,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching player data'
            ], 500);
        }
    }
} 