
# Open Minetopia Panel

A web panel for Minecraft players to manage their Minetopia account, plots, balance, and companies. This panel provides a seamless connection between your Minecraft server and web interface.

## Features
- 🔐 Secure authentication system
- 🎮 Minecraft account verification
- 🌓 Dark/Light mode support
- 📱 Responsive design
- 🔄 Real-time balance updates
- 🏠 Plot management
- 🏢 Company management

## Requirements
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Minecraft server with the Open Minetopia plugin

## Installation
Clone the repository
```bash
git clone https://github.com/yourusername/minetopia-panel.git
cd minetopia-panel
```
Install PHP dependecies
```bash
composer Install
```
Install and compile frontend dependecies
```bash
npm install
npm run build (or npm run dev when testing)
```
Environment setup
```bash
cp .env.example .env
```
Generate application key
```bash
php artisan key:generate 
```
Generate Minecraft API key (add this to your .env file)
```bash
php artisan tinker
echo bin2hex(random_bytes(32));
```
Configure your .env file:
`APP_NAME=`

`APP_URL=`

`DB_CONNECTION=`

`DB_HOST=`

`DB_PORT=`

`DB_DATABASE`

`DB_USERNAME=`

`DB_PASSWORD=`


Add the generated API key

`MINECRAFT_API_KEY=your-generated-key`

Run your migrations
```bash
php artisan migrate
```
Set up storage link
```bash
php artisan storage:link
```

## Minecraft Plugin Configuration
1.  Add the API key to your Minecraft plugin configuration
2. Configure the API endpoint in your plugin:

## Contributing
1. Fork the repository
2.  Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4.  Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License
TO DO

## Support
TO DO

## Credits
TO DO
