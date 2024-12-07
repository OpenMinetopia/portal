<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AssignRole extends Command
{
    protected $signature = 'role:assign';
    protected $description = 'Assign a role to a user';

    public function handle()
    {
        // Get all roles first to check if any exist
        $roles = Role::all();

        if ($roles->isEmpty()) {
            $this->error('No roles found in the database. Please run migrations and seeders first:');
            $this->info('php artisan migrate --seed');
            return 1;
        }

        // Get and validate Minecraft username
        $username = $this->askForUsername();
        if (!$username) return 1;

        // Get and validate role selection
        $role = $this->askForRole($roles);
        if (!$role) return 1;

        // Get user and current roles
        $user = User::where('minecraft_username', $username)->first();
        $currentRoles = $user->roles->pluck('name')->toArray();

        // Show current roles
        $this->showCurrentRoles($username, $currentRoles);

        // Confirm admin role assignment
        if ($role->is_admin && !$this->confirmAdminRole($username)) {
            return 1;
        }

        // Handle role assignment
        $this->assignRole($user, $role, $currentRoles);

        return 0;
    }

    protected function askForUsername()
    {
        $username = $this->ask('Enter Minecraft username');
        $user = User::where('minecraft_username', $username)->first();

        if (!$user) {
            $this->error("User with Minecraft username '{$username}' not found!");
            return null;
        }

        return $username;
    }

    protected function askForRole($roles)
    {
        // Create an array of choices with role IDs as keys
        $choices = [];
        foreach ($roles as $role) {
            $type = match(true) {
                $role->is_admin => '🛡️ [Admin]',
                $role->is_game_role => '🎮 [Game]',
                default => '👤 [Regular]'
            };
            
            $choices[$role->id] = "{$role->name} {$type}" . ($role->description ? " - {$role->description}" : "");
        }

        // Show available roles
        $this->info("\nAvailable roles:");
        foreach ($choices as $id => $label) {
            $this->line("  [{$id}] {$label}");
        }

        // Ask for role ID directly
        $roleId = $this->ask("\nEnter the role ID you want to assign");

        // Validate the role ID
        if (!is_numeric($roleId) || !isset($choices[$roleId])) {
            $this->error('Invalid role ID selected!');
            return null;
        }

        $role = Role::find($roleId);
        if (!$role) {
            $this->error('Role not found in database!');
            return null;
        }

        return $role;
    }

    protected function showCurrentRoles(string $username, array $currentRoles)
    {
        $this->info("\nCurrent roles for {$username}:");
        if (empty($currentRoles)) {
            $this->line('  No roles assigned');
        } else {
            foreach ($currentRoles as $roleName) {
                $this->line("  - {$roleName}");
            }
        }
    }

    protected function confirmAdminRole(string $username)
    {
        $this->warn("\n⚠️  Warning: You are about to assign an admin role!");
        if (!$this->confirm("Are you absolutely sure you want to make {$username} an administrator?", false)) {
            $this->info('Operation cancelled.');
            return false;
        }
        return true;
    }

    protected function assignRole(User $user, Role $role, array $currentRoles)
    {
        // Ask about replacement only if user has existing roles
        $replace = !empty($currentRoles) && 
            $this->confirm('Do you want to remove all existing roles first?', false);

        try {
            if ($replace) {
                $user->roles()->sync([$role->id]);
                $this->info("\n✅ Replaced all roles for {$user->minecraft_username} with {$role->name}");
            } else {
                // Check if role is already assigned
                if ($user->roles->contains($role->id)) {
                    $this->warn("\n⚠️  User already has the '{$role->name}' role!");
                    return;
                }
                
                $user->roles()->attach($role->id);
                $this->info("\n✅ Added {$role->name} role to {$user->minecraft_username}");
            }

            // Show updated roles
            $this->info("\nUpdated roles for {$user->minecraft_username}:");
            foreach ($user->fresh()->roles as $updatedRole) {
                $this->line("  - {$updatedRole->name}");
            }
        } catch (\Exception $e) {
            $this->error("Error assigning role: {$e->getMessage()}");
            $this->error("Please try again or check the logs for more information.");
            \Log::error('Role assignment error', [
                'user' => $user->minecraft_username,
                'role' => $role->name,
                'error' => $e->getMessage()
            ]);
        }
    }
}
