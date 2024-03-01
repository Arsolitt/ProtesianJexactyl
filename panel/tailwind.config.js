const { blue, zinc, cyan } = require('tailwindcss/colors');

module.exports = {
    content: ['./resources/scripts/**/*.{js,ts,tsx}'],
    theme: {
        extend: {
            backgroundImage: {
                storeone:
                    "url('https://www.minecraft.net/content/dam/games/minecraft/key-art/MC-Vanilla_Block-Column-Image_Boat-Trip_800x800.jpg')",
                storetwo:
                    "url('https://www.minecraft.net/content/dam/games/minecraft/key-art/MC-Vanilla_Block-Column-Image_Beach-Cabin_800x800.jpg')",
                storethree:
                    "url('https://www.minecraft.net/content/dam/games/minecraft/key-art/MC-Vanilla_Block-Column-Image_Mining_800x800.jpg')",
            },
            colors: {
                black: '#000',
                // "primary" and "neutral" are deprecated.
                primary: blue,
                neutral: zinc,

                // Use cyan / gray instead.
                gray: zinc,
                cyan: cyan,
                inert: {
                    50: '#f6f7f9',
                    100: '#ebedf3',
                    200: '#d3d7e4',
                    300: '#adb5cc',
                    400: '#808db0',
                    500: '#5a688c',
                    600: '#4c587d',
                    700: '#3f4865',
                    800: '#373f55',
                    900: '#313649',
                    950: '#212430',
                },
                main: {
                    50: '#effcfb',
                    100: '#d6f7f7',
                    200: '#b1edf0',
                    300: '#7cdfe4',
                    400: '#3fc8d1',
                    500: '#27bfcc',
                    600: '#208a9a',
                    700: '#20707e',
                    800: '#235c67',
                    900: '#214d58',
                    950: '#10323c',
                },
                negative: {
                    50: '#fff1f2',
                    100: '#fee5e8',
                    200: '#fdced5',
                    300: '#faa7b3',
                    400: '#f7758b',
                    500: '#ef4464',
                    600: '#cb2049',
                    700: '#b91742',
                    800: '#9b163e',
                    900: '#85163a',
                    950: '#4a071b',
                },
                menuActive: {
                    50: '#effcfc',
                    100: '#d7f5f6',
                    200: '#b4eaed',
                    300: '#80dae0',
                    400: '#45c1cb',
                    500: '#29a4b1',
                    600: '#258595',
                    700: '#246b7a',
                    800: '#255965',
                    900: '#234b56',
                    950: '#12313a',
                },
            },
            fontSize: {
                '2xs': '0.625rem',
            },
            transitionDuration: {
                250: '250ms',
            },
            borderColor: (theme) => ({
                default: theme('colors.neutral.400', 'currentColor'),
            }),
        },
    },
    plugins: [
        require('@tailwindcss/line-clamp'),
        require('@tailwindcss/forms')({
            strategy: 'class',
        }),
    ],
};
