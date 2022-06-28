module.exports = {
    content: ["./templates/**/*.html.twig"],
    theme: {
        extend: {
            screens: {
                "mobile": "320px",
                "xs": "475px",
                "tablet": "860px",
                "desktop": "1390px"
            },
            left: {
                "1/5": "20%"
            },
            width: {
                "1/9": "11.1111111%"
            },
            maxWidth: {
                "8xl": "88rem"
            },
            colors: {
                "accent": {
                    "red": "#FFE0E0",
                    "green": "#D2F0CD",
                    "yellow": "#F9EDC7"
                }
            },
            fontFamily: {
                "clickntoulon": ["'Proxima Soft'", "sans-serif"]
            }
        }
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography")
    ],
};