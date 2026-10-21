/**
 * The appearance form: every change is applied to the page right away as a preview,
 * by setting the same CSS variables the branding partial renders. Saving makes it stick.
 */
export default ({ initial, saved, shades, neutrals, fonts, radii, logo }) => ({
    ...initial,
    logoPreview: logo,
    removeLogo: false,

    init() {
        this.$watch('accent_color', () => this.apply());
        ['neutrals', 'font', 'heading_font', 'radius'].forEach((key) => this.$watch(key, () => this.apply()));
        Object.keys(fonts).forEach((key) => this.loadFont(key));
        this.apply();
    },

    get dirty() {
        return Object.keys(saved).some((key) => this[key] !== saved[key]);
    },

    use(theme) {
        Object.assign(this, theme);
    },

    pickLogo(event) {
        const file = event.target.files[0];
        this.removeLogo = false;
        this.logoPreview = file ? URL.createObjectURL(file) : logo;
    },

    loadFont(key) {
        if (document.querySelector(`link[data-font="${key}"]`)) return;
        const link = Object.assign(document.createElement('link'), { rel: 'stylesheet', href: fonts[key].url });
        link.dataset.font = key;
        document.head.append(link);
    },

    apply() {
        const root = document.documentElement.style;
        const channels = (hex) => [1, 3, 5].map((i) => parseInt(hex.slice(i, i + 2), 16));

        if (/^#[0-9a-f]{6}$/i.test(this.accent_color)) {
            const rgb = channels(this.accent_color);
            Object.entries(shades).forEach(([shade, mix]) => {
                const target = mix > 0 ? 255 : 0;
                root.setProperty(`--accent-${shade}`, rgb.map((c) => Math.round(c + (target - c) * Math.abs(mix))).join(' '));
            });
        }

        neutrals[this.neutrals]?.shades.forEach((hex, i) => {
            root.setProperty(`--gray-${Object.keys(shades)[i]}`, channels(hex).join(' '));
        });

        if (fonts[this.font]) {
            this.loadFont(this.font);
            root.setProperty('--font-sans', `"${fonts[this.font].name}"`);
        }

        if (fonts[this.heading_font]) {
            this.loadFont(this.heading_font);
            root.setProperty('--font-heading', `"${fonts[this.heading_font].name}"`);
        } else {
            root.setProperty('--font-heading', 'var(--font-sans)');
        }

        radii[this.radius]?.sizes.forEach((px, i) => {
            root.setProperty(`--radius-${['md', 'lg', 'xl'][i]}`, `${px}px`);
        });
    },
});
