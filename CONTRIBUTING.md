# Bijdragen aan OpenMinetopia Portal

Fijn dat je wilt helpen! Bugs melden, ideeën delen en pull requests zijn allemaal welkom.

## Een bug of idee

- Zoek eerst of er al een [issue](https://github.com/OpenMinetopia/portal/issues) over is.
- Gebruik het formulier voor een bug of een idee; hoe meer details (versie, stappen, foutmelding), hoe sneller het opgelost is.
- Voor vragen en hulp bij installeren is [Discord](https://discord.gg/6E3p8mPSVf) de snelste plek.
- Een beveiligingsprobleem meld je niet in een issue: zie [SECURITY.md](SECURITY.md).

## Lokaal draaien

Je hebt PHP 8.4, Composer, Node.js 22 en MySQL of MariaDB nodig.

```bash
git clone https://github.com/OpenMinetopia/portal.git
cd portal
composer install
npm ci && npm run build
php artisan portal:install
php artisan serve
```

Zonder Minecraft-server kun je het portaal vullen met demodata:

```bash
# In .env: PORTAL_DEMO=true
php artisan db:seed --class=DemoSeeder
```

Log daarna in met `demo@openminetopia.nl` / `demo1234`, of open `/demo`.

## Tests

De tests gebruiken MySQL of MariaDB op `127.0.0.1:3306` met gebruiker `root` zonder wachtwoord (zie `phpunit.xml`). Ze maken hun eigen databases aan (`omt_portal_test*`).

```bash
vendor/bin/pest
```

Er zijn twee soorten tests:

- `tests/Single`: een eigen portaal (`PORTAL_MODE=single`);
- `tests/Feature`: het gehoste platform met meerdere portalen (`PORTAL_MODE=hosted`).

## Een thema toevoegen

De thema's onder **Beheer → Uiterlijk & logo** zijn de bestanden in `resources/themes`. Maak je thema in je eigen portaal, kies **Exporteren**, vul `author` en `description` in en zet het bestand in die map. Een thema bevat alleen een accentkleur en de namen van de grijstinten, het lettertype en de hoeken uit `BrandingSetting`; eigen CSS, lettertypes van elders of een logo kunnen er niet in.

## Pull requests

- Eén onderwerp per pull request, met tests voor nieuw gedrag.
- Commit-berichten in één regel, zoals `fix: ...`, `feat: ...` of `docs: ...`.
- Teksten in het portaal zijn Nederlands.
- Door bij te dragen ga je akkoord dat je werk onder de [GPL-3.0-licentie](LICENSE) valt.
