<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class MinecraftTestConnectionCommand extends Command
{
    protected $signature = 'minecraft:test-connection';
    protected $description = 'Test de connectie met de Minecraft server';

    public function handle(): int
    {
        $this->info('🎮 Minecraft Server Connectie Test');
        $this->newLine();

        // Check if configuration exists
        if (!config('minecraft.server_address') || !config('minecraft.api_key')) {
            $this->error('❌ Configuratie ontbreekt!');
            $this->line('Controleer of deze waarden zijn ingesteld in je .env:');
            $this->line('- MC_SERVER_ADDRESS');
            $this->line('- MINECRAFT_API_KEY');
            return Command::FAILURE;
        }

        $this->info('Configuratie gevonden:');
        $this->line('Server: ' . config('minecraft.server_address'));
        $this->line('API Key: ' . substr(config('minecraft.api_key'), 0, 8) . '********');
        
        $this->newLine();
        $this->info('Test verbinding...');

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'X-API-Key' => config('minecraft.api_key'),
                    'Accept' => 'application/json'
                ])
                ->get(config('minecraft.server_address') . '/api/status');

            if ($response->successful()) {
                $this->newLine();
                $this->info('✅ Verbinding succesvol!');
                $this->table(
                    ['Status', 'Spelers', 'TPS', 'Uptime'],
                    [[
                        'Online',
                        $response->json('players_online') . '/' . $response->json('players_max'),
                        $response->json('tps'),
                        $response->json('uptime')
                    ]]
                );
                return Command::SUCCESS;
            }

            $this->error('❌ Server reageerde met status: ' . $response->status());
            $this->line('Response: ' . $response->body());
            return Command::FAILURE;

        } catch (\Exception $e) {
            $this->error('❌ Connectie mislukt!');
            $this->line('Error: ' . $e->getMessage());
            
            $this->newLine();
            $this->info('Troubleshooting:');
            $this->line('1. Controleer of de server online is');
            $this->line('2. Verifieer het server adres in .env');
            $this->line('3. Controleer de API key');
            $this->line('4. Check firewall instellingen');
            
            return Command::FAILURE;
        }
    }
} 