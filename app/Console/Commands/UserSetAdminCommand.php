<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use function Laravel\Prompts\search;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class UserSetAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:set-admin 
                          {user? : The ID or email of the user}
                          {--r|role-id= : The ID of the admin role (defaults to 1)}
                          {--f|force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a user as an administrator';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get or search for user
        $user = $this->getUserInteractively();
        
        if (!$user) {
            $this->error('User not found.');
            return Command::FAILURE;
        }

        // Get or select role
        $role = $this->getRoleInteractively();
        
        if (!$role) {
            $this->error('Admin role not found.');
            return Command::FAILURE;
        }

        // Confirm unless --force is used
        if (!$this->option('force') && !$this->confirmOperation($user, $role)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        try {
            // Check if user already has the role
            if ($user->hasRole($role)) {
                $this->warn("User '{$user->name}' already has the role '{$role->name}'.");
                return Command::SUCCESS;
            }

            // Attach the role
            $user->roles()->attach($role->id);

            $this->info("✓ Successfully made '{$user->name}' an administrator with role '{$role->name}'.");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to set admin role: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Get user through interactive search if not provided.
     */
    private function getUserInteractively(): ?User
    {
        // If user ID/email is provided directly
        if ($identifier = $this->argument('user')) {
            return User::where('id', $identifier)
                      ->orWhere('email', $identifier)
                      ->first();
        }

        // Interactive user search
        $userId = search(
            label: 'Search for a user by name or email:',
            placeholder: 'Start typing to search...',
            options: function (string $value) {
                return User::where('name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->get()
                    ->mapWithKeys(fn ($user) => [
                        $user->id => "{$user->name} ({$user->email})"
                    ])
                    ->all();
            }
        );

        return User::find($userId);
    }

    /**
     * Get role through interactive selection if not provided.
     */
    private function getRoleInteractively(): ?Role
    {
        // If role ID is provided directly
        if ($roleId = $this->option('role-id')) {
            return Role::find($roleId);
        }

        // Get all admin roles
        $roles = Role::where('name', 'like', '%admin%')
                    ->orWhere('name', 'like', '%administrator%')
                    ->get();

        if ($roles->isEmpty()) {
            return Role::find(1); // Fallback to ID 1
        }

        // Interactive role selection
        $roleId = select(
            label: 'Select the admin role to assign:',
            options: $roles->mapWithKeys(fn ($role) => [
                $role->id => "{$role->name} (ID: {$role->id})"
            ])->all(),
            default: $roles->first()->id
        );

        return Role::find($roleId);
    }

    /**
     * Confirm the operation with the user.
     */
    private function confirmOperation(User $user, Role $role): bool
    {
        $this->newLine();
        $this->info('User Details:');
        $this->table(
            ['ID', 'Name', 'Email'],
            [[$user->id, $user->name, $user->email]]
        );

        $this->info('Role Details:');
        $this->table(
            ['ID', 'Name'],
            [[$role->id, $role->name]]
        );

        return confirm(
            label: 'Do you want to proceed with making this user an administrator?',
            default: true
        );
    }
} 