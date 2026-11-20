/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                canvas: '#F3F4F6',
                surface: '#FFFFFF',
                ink: '#0F0F10',
                'text-main': '#111827',
                'text-muted': '#6B7280',
                strip: '#FAFAFA',
                line: '#E5E7EB',
            },
            boxShadow: {
                card: '0 2px 14px rgba(0, 0, 0, 0.03)',
                pill: '0 6px 16px rgba(15, 15, 16, 0.14)',
            },
        },
    },
    plugins: [],
};
