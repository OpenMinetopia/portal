<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:generate
                          {--f|force : Skip confirmation and overwrite existing tokens}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate secure API tokens for Minecraft and Plugin integration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Check if tokens already exist in .env
        if (!$this->option('force') && 
            (env('MINECRAFT_API_KEY') || env('PLUGIN_API_KEY'))) {
            
            if (!$this->confirm('API tokens already exist in your .env file. Do you want to regenerate them?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        // Generate tokens
        $minecraftToken = 'mct_' . Str::random(32);
        $pluginToken = 'plt_' . Str::random(32);

        // Update .env file
        $this->updateEnvFile($minecraftToken, $pluginToken);

        // Display the tokens
        $this->newLine();
        $this->info('API tokens generated successfully! 🔑');
        $this->newLine();

        $this->table(
            ['Key', 'Value'],
            [
                ['MINECRAFT_API_KEY', $minecraftToken],
                ['PLUGIN_API_KEY', $pluginToken],
            ]
        );

        $this->newLine();
        $this->info('These tokens have been automatically added to your .env file.');
        $this->warn('Make sure to restart your application if it\'s currently running.');
        
        return Command::SUCCESS;
    }

    /**
     * Update the .env file with new tokens.
     */
    private function updateEnvFile(string $minecraftToken, string $pluginToken): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        // Replace or add MINECRAFT_API_KEY
        if (strpos($envContent, 'MINECRAFT_API_KEY=') !== false) {
            $envContent = preg_replace(
                '/MINECRAFT_API_KEY=.*/',
                'MINECRAFT_API_KEY=' . $minecraftToken,
                $envContent
            );
        } else {
            $envContent .= "\nMINECRAFT_API_KEY=" . $minecraftToken;
        }

        // Replace or add PLUGIN_API_KEY
        if (strpos($envContent, 'PLUGIN_API_KEY=') !== false) {
            $envContent = preg_replace(
                '/PLUGIN_API_KEY=.*/',
                'PLUGIN_API_KEY=' . $pluginToken,
                $envContent
            );
        } else {
            $envContent .= "\nPLUGIN_API_KEY=" . $pluginToken;
        }

        // Save the updated content back to .env
        file_put_contents($envPath, $envContent);
    }
} 