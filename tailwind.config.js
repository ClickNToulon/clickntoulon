module.exports = {
    purge: [],
    darkMode: 'media', // or 'media' or 'class'
    theme: {
        extend: {
            screens: {
                'mobile': '320px',
                'tablet': '860px',
                'desktop': '1390px'
            },
            left: {
                '1/5': '20%'
            },
        }
    },
    variants: {
        extend: {
            borderWidth: ['dark'],
            boxShadow: ['dark'],
            backgroundColor: ['hover']
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/aspect-ratio'),
    ],

}