import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                gotix: {
                    amber: '#F59E0B',
                    ink: '#18181B',
                    paper: '#FAFAF9',
                    coral: '#E11D48',
                },
            },
            boxShadow: {
                'retro-xs': '1.5px 1.5px 0px #18181B',
                'retro-sm': '2px 2px 0px #18181B',
                'retro': '3px 3px 0px #18181B',
                'retro-lg': '5px 5px 0px #18181B',
                'retro-amber': '3px 3px 0px #F59E0B',
                'retro-coral': '3px 3px 0px #E11D48',
            },
            fontFamily: {
                sans: ['"DM Sans"', ...defaultTheme.fontFamily.sans],
                heading: ['"Bricolage Grotesque"', ...defaultTheme.fontFamily.sans],
                serif: ['"Iowan Old Style"', '"Palatino Linotype"', 'Palatino', '"Book Antiqua"', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [forms],
};
