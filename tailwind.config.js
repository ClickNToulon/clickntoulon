module.exports = {
    purge: [],
    darkMode: 'media', // or 'media' or 'class'
    theme: {
        screens: {
            'sm': '640px',
            // => @media (min-width: 640px) { ... }

            'md': '768px',
            // => @media (min-width: 768px) { ... }

            'tablet': '860px',
            // => @media (min-width: 860px) { ... }

            'lg': '1024px',
            // => @media (min-width: 1024px) { ... }

            'xl': '1280px',
            // => @media (min-width: 1280px) { ... }

            '2xl': '1536px',
            // => @media (min-width: 1536px) { ... }
        }
    },
    variants: {
        extend: {
            borderWidth: ['dark'],
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],

}