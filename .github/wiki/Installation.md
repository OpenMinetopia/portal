# Installatie Handleiding 🛠️

## Inhoudsopgave
- [Vereisten](#vereisten)
- [Stap-voor-stap Installatie](#stap-voor-stap-installatie)
- [Automatische Installatie](#automatische-installatie)
- [Handmatige Installatie](#handmatige-installatie)
- [Probleemoplossing](#probleemoplossing)

## Vereisten

Zorg dat je systeem aan deze minimale eisen voldoet:

- PHP 8.2 of hoger
- Composer
- Node.js & NPM
- MySQL/MariaDB of SQLite
- Git
- Redis (optioneel, maar aanbevolen)

## Stap-voor-stap Installatie

### 1. Repository Klonen 
```bash
git clone https://github.com/OpenMinetopia/portal.git
cd portal
```

### 2. Setup wizard gebruiken
De eenvoudigste manier om te installeren is via onze setup wizard:
```bash
php artisan panel:setup
```

De wizard helpt je met:
- Basis configuratie
- Database setup
- Mail configuratie
- Minecraft integratie
- API tokens genereren
- Redis setup (optioneel)

### 3. Eerste Admin Account

1. Registreer een account via de web interface
2. Maak de gebruiker administrator:
```bash
php artisan user:set-admin
```

## Probleemoplossing

### Veel voorkomende problemen
#### Composer memory issues
```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```

#### Storage Permissions
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

#### Database Issues
- Controleer je .env database configuratie
- Verifieer dat MySQL/MariaDB draait
- Check database gebruikersrechten

[Terug naar Home](Home)
