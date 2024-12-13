<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\password;

class PanelSetupCommand extends Command
{
    protected $signature = 'panel:setup {--force : Skip confirmations}';
    protected $description = 'Interactive setup wizard for OpenMinetopia Portal';

    private array $envValues = [];

    public function handle(): int
    {
        $this->info('╔═══════════════════════════════════════════╗');
        $this->info('║     OpenMinetopia Portal Setup Wizard     ║');
        $this->info('╚═══════════════════════════════════════════╝');
        $this->newLine();

        // Basic Setup
        $this->setupBasicConfig();
        
        // Database Configuration
        $this->setupDatabase();
        
        // Redis Configuration
        $this->setupRedis();
        
        // Mail Configuration
        $this->setupMail();
        
        // Minecraft Configuration
        $this->setupMinecraft();
        
        // Generate Tokens
        $this->generateTokens();
        
        // Write Configuration
        $this->writeConfiguration();
        
        // Final Steps
        $this->finalSteps();

        return Command::SUCCESS;
    }

    private function setupBasicConfig(): void
    {
        $this->info('Basic Configuration');
        $this->line('─────────────────────────────────────');

        $this->envValues['APP_NAME'] = text(
            label: 'What is your panel name?',
            placeholder: 'OpenMinetopia Portal',
            default: env('APP_NAME', 'OpenMinetopia Portal'),
        );

        $appUrl = text(
            label: 'What is your application URL?',
            placeholder: 'https://portal.example.com',
            default: env('APP_URL', 'http://localhost'),
            validate: fn (string $value) => filter_var($value, FILTER_VALIDATE_URL) ? null : 'Please enter a valid URL'
        );

        $this->envValues['APP_URL'] = $appUrl;
        $this->envValues['APP_ENV'] = 'production';
        $this->envValues['APP_DEBUG'] = 'false';
        $this->envValues['APP_KEY'] = $this->generateAppKey();
    }

    private function setupDatabase(): void
    {
        $this->newLine();
        $this->info('Database Configuration');
        $this->line('─────────────────────────────────────');

        $dbType = select(
            'Select database type:',
            [
                'mysql' => 'MySQL/MariaDB',
                'sqlite' => 'SQLite',
            ]
        );

        if ($dbType === 'mysql') {
            $this->envValues['DB_CONNECTION'] = 'mysql';
            $this->envValues['DB_HOST'] = text(
                label: 'Database host:',
                default: env('DB_HOST', '127.0.0.1')
            );
            $this->envValues['DB_PORT'] = text(
                label: 'Database port:',
                default: env('DB_PORT', '3306')
            );
            $this->envValues['DB_DATABASE'] = text(
                label: 'Database name:',
                default: env('DB_DATABASE', 'openminetopia')
            );
            $this->envValues['DB_USERNAME'] = text(
                label: 'Database username:',
                default: env('DB_USERNAME', 'root')
            );
            $this->envValues['DB_PASSWORD'] = password(
                label: 'Database password:'
            );
        } else {
            $this->envValues['DB_CONNECTION'] = 'sqlite';
            touch(database_path('database.sqlite'));
            $this->info('Created SQLite database file');
        }
    }

    private function setupRedis(): void
    {
        $this->newLine();
        $this->info('Redis Configuration');
        $this->line('─────────────────────────────────────');

        if (confirm('Would you like to use Redis for cache and queues?', true)) {
            $this->envValues['REDIS_HOST'] = text(
                label: 'Redis host:',
                default: env('REDIS_HOST', '127.0.0.1')
            );
            $this->envValues['REDIS_PASSWORD'] = password(
                label: 'Redis password (leave empty if none):'
            );
            $this->envValues['REDIS_PORT'] = text(
                label: 'Redis port:',
                default: env('REDIS_PORT', '6379')
            );

            // Test Redis connection
            try {
                Redis::connect($this->envValues['REDIS_HOST'], $this->envValues['REDIS_PORT']);
                $this->info('✓ Redis connection successful');
                
                $this->envValues['CACHE_DRIVER'] = 'redis';
                $this->envValues['QUEUE_CONNECTION'] = 'redis';
                $this->envValues['SESSION_DRIVER'] = 'redis';
            } catch (\Exception $e) {
                $this->warn('Could not connect to Redis. Falling back to file/database drivers');
                $this->envValues['CACHE_DRIVER'] = 'file';
                $this->envValues['QUEUE_CONNECTION'] = 'database';
                $this->envValues['SESSION_DRIVER'] = 'file';
            }
        } else {
            $this->envValues['CACHE_DRIVER'] = 'file';
            $this->envValues['QUEUE_CONNECTION'] = 'database';
            $this->envValues['SESSION_DRIVER'] = 'file';
        }
    }

    private function setupMail(): void
    {
        $this->newLine();
        $this->info('Mail Configuration');
        $this->line('─────────────────────────────────────');

        $mailDriver = select(
            'Select mail driver:',
            [
                'smtp' => 'SMTP Server',
                'php' => 'PHP Mail',
            ]
        );

        if ($mailDriver === 'smtp') {
            $this->envValues['MAIL_MAILER'] = 'smtp';
            $this->envValues['MAIL_HOST'] = text(
                label: 'SMTP host:',
                default: env('MAIL_HOST', 'smtp.gmail.com')
            );
            $this->envValues['MAIL_PORT'] = text(
                label: 'SMTP port:',
                default: env('MAIL_PORT', '587')
            );
            $this->envValues['MAIL_USERNAME'] = text(
                label: 'SMTP username:',
                default: env('MAIL_USERNAME', '')
            );
            $this->envValues['MAIL_PASSWORD'] = password(
                label: 'SMTP password:'
            );
            $this->envValues['MAIL_ENCRYPTION'] = select(
                'Select mail encryption:',
                [
                    'tls' => 'TLS',
                    'ssl' => 'SSL',
                    'null' => 'None'
                ]
            );
        } else {
            $this->envValues['MAIL_MAILER'] = 'php';
        }

        $this->envValues['MAIL_FROM_ADDRESS'] = text(
            label: 'From address:',
            default: env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            validate: fn (string $value) => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : 'Please enter a valid email'
        );
        $this->envValues['MAIL_FROM_NAME'] = text(
            label: 'From name:',
            default: env('MAIL_FROM_NAME', $this->envValues['APP_NAME'])
        );
    }

    private function setupMinecraft(): void
    {
        $this->newLine();
        $this->info('Minecraft Configuration');
        $this->line('─────────────────────────────────────');

        $this->envValues['MC_SERVER_ADDRESS'] = text(
            label: 'Minecraft server address:',
            placeholder: 'play.example.com',
            validate: fn (string $value) => !empty($value) ? null : 'Server address is required'
        );
    }

    private function generateTokens(): void
    {
        $this->envValues['MINECRAFT_API_KEY'] = 'mct_' . Str::random(32);
        $this->envValues['PLUGIN_API_KEY'] = 'plt_' . Str::random(32);
    }

    private function generateAppKey(): string
    {
        return 'base64:' . base64_encode(random_bytes(32));
    }

    private function writeConfiguration(): void
    {
        $this->newLine();
        $this->info('Writing Configuration');
        $this->line('─────────────────────────────────────');

        $envFile = base_path('.env');
        $envExample = base_path('.env.example');

        // Create .env from .env.example if it doesn't exist
        if (!File::exists($envFile) && File::exists($envExample)) {
            File::copy($envExample, $envFile);
        }

        // Read current env content
        $envContent = File::exists($envFile) ? file_get_contents($envFile) : '';

        // Update each configuration value
        foreach ($this->envValues as $key => $value) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}=" . (str_contains($value, ' ') ? '"'.$value.'"' : $value),
                $envContent
            );
        }

        // Save the updated content
        File::put($envFile, $envContent);
        $this->info('✓ Configuration written successfully');
    }

    private function finalSteps(): void
    {
        $this->newLine();
        $this->info('Setup Complete! 🎉');
        $this->line('─────────────────────────────────────');

        // Ask about migrations
        if ($this->confirm('Would you like to run the database migrations now?', true)) {
            $this->info('Running migrations...');
            $this->call('migrate');
            $this->info('✓ Migrations completed');
        } else {
            $this->warn('Don\'t forget to run migrations later using: php artisan migrate');
        }

        $this->newLine();
        $this->info('Next required steps:');
        $this->line('1. Create a user account through the web interface');
        $this->line('2. Make your user an administrator: php artisan user:set-admin');
        $this->line('3. Start the queue worker: php artisan queue:work');
        if ($this->envValues['QUEUE_CONNECTION'] === 'redis') {
            $this->line('4. Ensure Redis server is running');
        }

        $this->newLine();
        $this->info('Your API tokens:');
        $this->line('MINECRAFT_API_KEY: ' . $this->envValues['MINECRAFT_API_KEY']);
        $this->line('PLUGIN_API_KEY: ' . $this->envValues['PLUGIN_API_KEY']);

        // Ask to star the repository
        $this->newLine();
        if (confirm(
            label: 'Would you like to show some love by starring our repository?',
            default: true,
            hint: 'This helps us grow the project and motivates us to keep improving!'
        )) {
            // Open the repository based on the operating system
            if (PHP_OS_FAMILY === 'Darwin') {
                exec('open https://github.com/OpenMinetopia/portal');
            } elseif (PHP_OS_FAMILY === 'Linux') {
                exec('xdg-open https://github.com/OpenMinetopia/portal');
            } elseif (PHP_OS_FAMILY === 'Windows') {
                exec('start https://github.com/OpenMinetopia/portal');
            }
            
            $this->info('Thank you for your support! 💖');
        }

        $this->newLine();
        $this->info('Setup completed successfully! Enjoy using OpenMinetopia Portal! 🎮');
    }
} 