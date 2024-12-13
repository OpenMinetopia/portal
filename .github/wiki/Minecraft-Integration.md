# Minecraft Integratie 🎮

## Overzicht

OpenMinetopia Portal communiceert met je Minecraft server via een speciale plugin en API tokens.

## Vereisten

- Minecraft server (Paper/Spigot)
- OpenMinetopia Plugin
- Geldige API tokens
- Correcte server configuratie

## Setup

### 1. API Tokens

Genereer tokens via: 
```bash
git clone https://github.com/OpenMinetopia/portal
cd portal
php artisan tokens:generate
```

Of gebruik de setup wizard:
```php
php artisan panel:setup
```

### 2. Plugin Configuratie

1. Download de OpenMinetopia plugin
2. Plaats in plugins folder
3. Start/herstart de server
4. Configureer in `plugins/OpenMinetopia/config.yml`:

```yml
url: "https://jouw-portal-url.nl"
minecraft_token: "mct_xxx"
plugin_token: "plt_xxx"
```

## Features

### Real-time Synchronisatie
- Bankrekeningen
- Plot eigendom
- Bedrijfsgegevens
- Vergunningen

### Automatische Updates
- Saldo wijzigingen
- Plot transacties
- Bedrijfsactiviteiten
- Strafblad mutaties

## Probleemoplossing

### Connectie Testen
```bash
php artisan minecraft:test-connection
```

### Veel Voorkomende Problemen

#### API Timeout
- Controleer firewall instellingen
- Verifieer server URL
- Check API tokens

#### Synchronisatie Issues
- Clear server cache
- Herstart plugin
- Controleer logs

## Beveiliging

### Best Practices
- Gebruik HTTPS
- Roteer tokens regelmatig
- Monitor API gebruik
- Beperk IP toegang

### Token Veiligheid
- Deel tokens nooit publiekelijk
- Sla tokens veilig op
- Gebruik sterke tokens

## Plugin Ontwikkeling

### API Documentatie
Zie [API Docs](API-Documentation) voor endpoints en authenticatie.

### Events
De plugin triggered events voor:
- Transacties
- Plot wijzigingen
- Bedrijfsacties
- Vergunningen

[Terug naar Home](Home)
