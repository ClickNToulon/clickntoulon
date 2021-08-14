module.exports = {
    purge: [],
    darkMode: 'media', // or 'media' or 'class'
    theme: {
        extend: {
            screens: {
                'mobile': '320px',
                'tablet': '860px',
            },
        }
    },
    variants: {
        extend: {
            borderWidth: ['dark'],
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/aspect-ratio'),
    ],

}