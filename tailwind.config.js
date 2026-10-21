import defaultTheme from 'tailwindcss/defaultTheme';

const themeColor = (name) => Object.fromEntries(
    [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950]
        .map((shade) => [shade, `rgb(var(--${name}-${shade}) / <alpha-value>)`]),
);

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
                sans: ['var(--font-sans)', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                // Neutrals and accent come from the theme variables in app.css, so admins can change them.
                gray: themeColor('gray'),
                // The accent. Named indigo so every existing button, link and focus ring picks it up.
                indigo: themeColor('accent'),
                brand: 'rgb(var(--accent-300) / <alpha-value>)',
            },
            borderRadius: {
                md: 'var(--radius-md)',
                lg: 'var(--radius-lg)',
                xl: 'var(--radius-xl)',
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
