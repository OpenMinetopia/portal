import defaultTheme from 'tailwindcss/defaultTheme';

/**
 * The OpenMinetopia look: warm neutrals instead of cool grays, and the logo's
 * green as the accent. The views keep using `gray-*` and `indigo-*` classes;
 * these palettes give them the new colours.
 *
 * @type {import('tailwindcss').Config}
 */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Schibsted Grotesk"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                gray: {
                    50: '#f4f4f1',
                    100: '#f0f0ec',
                    200: '#e3e3de',
                    300: '#cfcfc8',
                    400: '#9a9a93',
                    500: '#6b6b65',
                    600: '#55554f',
                    700: '#2d2d2b',
                    800: '#1c1c1b',
                    900: '#131313',
                    950: '#0b0b0a',
                },
                // The accent. Named indigo so every existing button, link and focus ring picks it up.
                indigo: {
                    50: '#eef7ea',
                    100: '#dcefd3',
                    200: '#bfe3ad',
                    300: '#93d879',
                    400: '#79c160',
                    500: '#4e7f3c',
                    600: '#3f6b30',
                    700: '#345a28',
                    800: '#2a4721',
                    900: '#1f3519',
                    950: '#12200e',
                },
                brand: '#93d879',
            },
            borderRadius: {
                md: '6px',
                lg: '8px',
                xl: '10px',
            },
            boxShadow: {
                // Cards are outlined, not lifted: the small shadows become nothing.
                sm: '0 0 #0000',
                DEFAULT: '0 0 #0000',
            },
            keyframes: {
                'omt-land': {
                    from: { transform: 'translateY(-10px)', opacity: '0' },
                    to: { transform: 'none', opacity: '1' },
                },
                'omt-rise': {
                    from: { transform: 'translateY(8px)', opacity: '0' },
                    to: { transform: 'none', opacity: '1' },
                },
            },
            animation: {
                'omt-land': 'omt-land 600ms cubic-bezier(.3, 1.6, .5, 1) both',
                'omt-rise': 'omt-rise 500ms cubic-bezier(.2, .8, .2, 1) both',
            },
        },
    },
    plugins: [],
};
