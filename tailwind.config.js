const defaultTheme = require("tailwindcss/defaultTheme");
const svgToDataUri = require("mini-svg-data-uri");

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            backgroundImage: (theme) => ({
                "multiselect-caret": `url("${svgToDataUri(
                    `<svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.00048 3.78047L8.30048 0.480469L9.24315 1.42314L5.00048 5.6658L0.757812 1.42314L1.70048 0.480469L5.00048 3.78047Z" fill="#C1C9DE"/>
                    </svg>`
                )}")`,
                "multiselect-spinner": `url("${svgToDataUri(
                    `<svg viewBox="0 0 512 512" fill="${theme(
                        "colors.green.500"
                    )}" xmlns="http://www.w3.org/2000/svg"><path d="M456.433 371.72l-27.79-16.045c-7.192-4.152-10.052-13.136-6.487-20.636 25.82-54.328 23.566-118.602-6.768-171.03-30.265-52.529-84.802-86.621-144.76-91.424C262.35 71.922 256 64.953 256 56.649V24.56c0-9.31 7.916-16.609 17.204-15.96 81.795 5.717 156.412 51.902 197.611 123.408 41.301 71.385 43.99 159.096 8.042 232.792-4.082 8.369-14.361 11.575-22.424 6.92z"></path></svg>`
                )}")`,
                "multiselect-remove": `url("${svgToDataUri(
                    `<svg viewBox="0 0 320 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M207.6 256l107.72-107.72c6.23-6.23 6.23-16.34 0-22.58l-25.03-25.03c-6.23-6.23-16.34-6.23-22.58 0L160 208.4 52.28 100.68c-6.23-6.23-16.34-6.23-22.58 0L4.68 125.7c-6.23 6.23-6.23 16.34 0 22.58L112.4 256 4.68 363.72c-6.23 6.23-6.23 16.34 0 22.58l25.03 25.03c6.23 6.23 16.34 6.23 22.58 0L160 303.6l107.72 107.72c6.23 6.23 16.34 6.23 22.58 0l25.03-25.03c6.23-6.23 6.23-16.34 0-22.58L207.6 256z"></path></svg>`
                )}")`,
            }),
            colors: {
                //green
                primary: {
                    100: "#d6dfd9",
                    200: "#66ff99",
                    300: "#33cc66",
                    400: "#339933",
                    500: "#315d3f",
                    600: "#284a32",
                    700: "#1c4629",
                    800: "#163c22",
                    900: "#0e3019",
                },
                //orange
                secondary: {
                    100: "#fce7d6",
                    200: "#f9e2bb",
                    300: "#f9d492",
                    400: "#ffcc66",
                    500: "#e8a838",
                    600: "#ee8934",
                    700: "#c1861f",
                    800: "#663300",
                    900: "#330000",
                },
                danger: {
                    100: "#ffcccc",
                    200: "#ff9999",
                    300: "#ff6666",
                    400: "#ff3333",
                    500: "#e40000",
                    600: "#c20707",
                    700: "#a10606",
                    800: "#7a0404",
                    900: "#480202",
                },
                dark: "#292d32",
                grey: "#939393", // darker grey
                mainbg: "#e8eae9", // soft grey
                blue: {
                    DEFAULT: "#1269FB",
                    50: "#eef7ff",
                    100: "#d8ecff",
                    200: "#badeff",
                    300: "#8accff",
                    400: "#54aeff",
                    500: "#2c8bff",
                    600: "#1269fb",
                    700: "#0e54e7",
                    800: "#1244bb",
                    900: "#153e93",
                    950: "#122659",
                }, // #1269FB
                fileinputbg: "#eaf1f8",
            },
            fontFamily: {
                sans: ["Urbanist", ...defaultTheme.fontFamily.sans],
            },
            width: {
                sidebar: "280px",
            },
            height: {
                applayout: "calc(100vh - 32px)",
                applayout_footer: "32px",
            },
        },
    },

    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
    ],
};
