import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./vendor/robsontenorio/mary/src/View/Components/**/*.php",
        "./app/livewire/**/*.php",
        "./node_modules/flowbite/**/*.js",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
                "noto-kufi-arabic": ['"Noto Kufi Arabic"', "sans-serif"],
                caudex: ["Caudex", "serif"],
                lato: ["Lato", "sans-serif"],
                david: ["David", "serif"],
            },
            colors: {
                primary: "#4FB783",
                secondary: "#409D9B",
                accent: "#EBFFF5",
                cultured: "#F6F6F6",
                rasinblack: "#222222",
                myblue: "#3E68FF",
                myred: "#DC143C",
            },
        },
    },

    plugins: [forms, require("flowbite/plugin"), require("daisyui")],

    daisyui: {
        themes: [
            {
                cupcake: {
                    ...require("daisyui/src/theming/themes")["cupcake"],
                    primary: "#4FB783",
                    secondary: "#409D9B",
                    accent: "#EBFFF5",
                    cultured: "#F6F6F6",
                    rasinblack: "#222222",
                    // info: "#3E68FF",
                    // error: "#DC143C",
                },
            },
        ],
    },
};
