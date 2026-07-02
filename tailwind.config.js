export default {
    content: [
        './resources/views/**/*.blade.php',
        './app/View/Components/**/*.php',
        './app/Http/Controllers/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                ink: '#111418',
                muted: '#647083',
                paper: '#f5f6f2',
                brand: {
                    DEFAULT: '#1f5fd6',
                    dark: '#1747a6',
                    soft: '#eef5ff',
                },
                mint: {
                    DEFAULT: '#08756f',
                    soft: '#e8fbf8',
                },
            },
            boxShadow: {
                soft: '0 18px 50px rgba(16, 20, 24, 0.08)',
                panel: '0 28px 72px rgba(16, 20, 24, 0.16)',
            },
            borderRadius: {
                panel: '8px',
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
            },
        },
    },
    corePlugins: {
        preflight: false,
    },
    plugins: [],
};
