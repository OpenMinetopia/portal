# Veelgestelde Vragen ❓

## Algemeen

### Wat is OpenMinetopia Portal?
OpenMinetopia Portal is een webapplicatie voor het beheren van Minetopia servers. Het biedt tools voor het beheren van plots, bankrekeningen, bedrijven en meer.

### Is het gratis te gebruiken?
Ja, OpenMinetopia Portal is volledig open-source en gratis te gebruiken onder de MIT licentie.

## Installatie

### Welke PHP versie heb ik nodig?
PHP 8.2 of hoger is vereist voor OpenMinetopia Portal.

### Kan ik SQLite gebruiken in plaats van MySQL?
Ja, het portal ondersteunt zowel MySQL/MariaDB als SQLite.

### Hoe update ik naar een nieuwe versie?
Zie onze [Update Handleiding](Updates) voor gedetailleerde instructies.

## Features

### Kan ik features uitschakelen die ik niet gebruik?
Ja, via het admin panel kun je individuele modules aan- en uitzetten.

### Hoe voeg ik een administrator toe?
Gebruik het commando `php artisan user:set-admin` na registratie van een gebruiker.

## Integratie

### Werkt het met elke Minecraft server?
Het portal is specifiek ontworpen voor Minetopia servers met de juiste plugins.

### Hoe koppel ik mijn Minecraft server?
Zie onze [Minecraft Integratie](Minecraft-Integration) handleiding.

## Problemen Oplossen

### De Redis connectie werkt niet
- Controleer of Redis is geïnstalleerd
- Verifieer de Redis configuratie in .env
- Check of de Redis service draait

### Mail versturen werkt niet
- Controleer je SMTP instellingen
- Test met `php artisan mail:test`
- Check de logs in `storage/logs`

### API tokens werken niet
- Genereer nieuwe tokens met `php artisan tokens:generate`
- Controleer of de tokens correct zijn geconfigureerd in de plugin
- Verifieer de server configuratie

## Support

### Waar kan ik bugs melden?
Bugs kun je melden via [GitHub Issues](https://github.com/OpenMinetopia/portal/issues).

### Hoe krijg ik support?
- Join onze [Discord](https://discord.gg/openminetopia)
- Open een GitHub issue
- Check de wiki documentatie

### Kan ik bijdragen aan het project?
Ja! Zie onze [Contributing Guidelines](Contributing) voor meer informatie. 