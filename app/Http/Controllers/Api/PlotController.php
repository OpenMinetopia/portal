<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PlotController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'description' => 'nullable|string',
                'world' => 'required|string',
                'coordinates' => 'required|array',
                'flags' => 'required|array'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $plot = Plot::create([
                'user_id' => $user->id,
                'description' => $validated['description'],
                'world' => $validated['world'],
                'coordinates' => $validated['coordinates'],
                'flags' => $validated['flags']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plot created successfully',
                'data' => ['plot_id' => $plot->id]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating plot', [
                'error' => $e->getMessage(),
                'minecraft_uuid' => $request->minecraft_uuid ?? null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'description' => 'nullable|string',
                'flags' => 'array'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $plot = Plot::find($id);
            if (!$plot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plot not found',
                    'error_code' => 'plot_not_found'
                ], 404);
            }

            if ($plot->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authorized to update this plot',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $plot->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Plot updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating plot', [
                'error' => $e->getMessage(),
                'plot_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }

    public function addMember(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'member_uuid' => 'required|string|size:32'
            ]);

            $owner = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner not found',
                    'error_code' => 'owner_not_found'
                ], 404);
            }

            $member = User::where('minecraft_uuid', $validated['member_uuid'])->first();
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found',
                    'error_code' => 'member_not_found'
                ], 404);
            }

            $plot = Plot::find($id);
            if (!$plot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plot not found',
                    'error_code' => 'plot_not_found'
                ], 404);
            }

            if ($plot->user_id !== $owner->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authorized to modify this plot',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $plot->members()->attach($member->id);

            return response()->json([
                'success' => true,
                'message' => 'Member added to plot successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding plot member', [
                'error' => $e->getMessage(),
                'plot_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }

    public function removeMember(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'member_uuid' => 'required|string|size:32'
            ]);

            $owner = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner not found',
                    'error_code' => 'owner_not_found'
                ], 404);
            }

            $member = User::where('minecraft_uuid', $validated['member_uuid'])->first();
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found',
                    'error_code' => 'member_not_found'
                ], 404);
            }

            $plot = Plot::find($id);
            if (!$plot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plot not found',
                    'error_code' => 'plot_not_found'
                ], 404);
            }

            if ($plot->user_id !== $owner->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authorized to modify this plot',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $plot->members()->detach($member->id);

            return response()->json([
                'success' => true,
                'message' => 'Member removed from plot successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing plot member', [
                'error' => $e->getMessage(),
                'plot_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }
} 