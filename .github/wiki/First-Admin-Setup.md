# Eerste Administrator Configuratie 👑

## Overzicht

Deze handleiding legt uit hoe je de eerste administrator account aanmaakt voor OpenMinetopia Portal.

## Stappen

### 1. Registreer een Account

1. Ga naar je portal URL
2. Klik op "Registreren"
3. Vul je gegevens in:
   - Minecraft gebruikersnaam
   - E-mailadres
   - Wachtwoord

### 2. Maak Administrator

Na registratie, gebruik het volgende commando om je account administrator te maken:

```bash
php artisan user:set-admin
```

De wizard zal je helpen bij:
- Het selecteren van je account
- Het toewijzen van de admin rol
- Het bevestigen van de actie

### 3. Verifieer Administrator Status

Na het uitvoeren van het commando:
1. Log uit en weer in
2. Je ziet nu het "Beheer" menu in de sidebar
3. Je hebt toegang tot:
   - Gebruikersbeheer
   - Plotbeheer
   - Rollenbeheer
   - Instellingen

## Administrator Rechten

Als administrator heb je toegang tot:

### 🔧 Systeembeheer
- Features in/uitschakelen
- API tokens beheren
- Server instellingen configureren

### 👥 Gebruikersbeheer
- Gebruikers bekijken/bewerken
- Rollen toewijzen
- Permissies beheren

### 🏘️ Plot Management
- Alle plots bekijken
- Plot eigendom aanpassen
- Plot instellingen beheren

### ⚙️ Configuratie
- Server instellingen
- Mail configuratie
- Feature toggles
- Integratie instellingen

## Volgende Stappen

Na het configureren van je admin account:
1. Configureer de [Features](Features)
2. Stel de [Minecraft Integratie](Minecraft-Integration) in
3. Beheer [Gebruikers en Rollen](Users-And-Roles)

## Veiligheid

- Gebruik een sterk wachtwoord
- Bewaar je inloggegevens veilig
- Maak niet onnodig veel administrators
- Monitor de audit logs

[Terug naar Home](Home) 