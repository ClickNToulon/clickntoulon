const colors = require("tailwindcss/colors");
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
            colors: {
                green: colors.emerald,
                yellow: colors.amber,
                sky: colors.sky,
                "darkblue": {
                    "600": "hsl(220, 95%, 30%)",
                    "700": "hsl(220, 95%, 20%)",
                    "800": "hsl(220, 95%, 15%)",
                    "900": "hsl(220, 95%, 10%)",
                    "underline": "rgb(29, 78, 216)"
                },
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
        require("@tailwindcss/aspect-ratio")
    ],
};