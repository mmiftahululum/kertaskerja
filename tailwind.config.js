import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
       "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    

    theme: {
       extend: {
                fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                },
                keyframes: {
                grow: {
                    '0%': { height: '0%' },
                    '100%': { height: '100%' },
                },
                },
                animation: {
                grow: 'grow 1s ease-out forwards',
                },
            },
    },

    plugins: [forms],
    safelist: [
          'fixed',
        'inset-0',
        'top-1/2',
        'left-1/2',
        '-translate-x-1/2',
        '-translate-y-1/2',
        'z-40', // Untuk overlay
        'z-50', // Untuk modal utama
        'overflow-y-auto', // Penting untuk scrollable modal
        // Tambahan lain yang mungkin perlu diamankan secara eksplisit jika belum tercakup
        'bg-opacity-75', // Jika Anda menggunakan opacity untuk overlay latar belakang
        'max-w-lg', // Jika 
        // explicit utilities
        'border-solid', 'border-dashed', 'border-dotted', 'border-none',
        'outline-none',
        'inline-flex','items-center','justify-between','justify-end','gap-1','gap-2','gap-3','gap-4',
        'px-1','px-2','px-3','px-4','px-5','py-1','py-2','py-3','py-4',
        'rounded','rounded-sm','rounded-md','rounded-lg','rounded-full',
        'shadow','shadow-sm','shadow-md','shadow-lg',
        'w-full','container','text-sm','text-base','text-lg','text-xs','text-white',
        'bg-white','bg-gray-50','bg-slate-100','bg-slate-200','bg-indigo-600','bg-indigo-700',
        'hover:bg-indigo-700','hover:bg-red-700','focus:outline-none','focus:ring-2','focus:ring-indigo-500',

        // color utility patterns (common color palette + shades)
        { pattern: /^(bg|text|border|ring|from|to|via)-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-(50|100|200|300|400|500|600|700|800|900)$/, variants: ['hover','focus','active','disabled','sm','md','lg'] },

        // single-tone color shortcuts (no shade)
        { pattern: /^(bg|text|border|ring)-(black|white)$/, variants: ['hover','focus','sm'] },

        // opacity helpers
        { pattern: /^bg-opacity-(?:0|5|10|20|25|30|40|50|60|70|75|80|90|100)$/ },

        // spacing and sizing patterns
        { pattern: /^(-)?(m|p)(t|r|b|l|x|y)?-(?:0|px|0\.5|1|1\.5|2|2\.5|3|3\.5|4|5|6|8|10|12|16|20|24|32|40|48|56|64)$/ , variants: ['sm','md','lg'] },
        { pattern: /^(w|h|max-w|min-w|max-h|min-h)-.*$/ },

        // grid / gap / cols
        { pattern: /^(grid|grid-cols|col-span|gap|gap-x|gap-y)-\d+$/, variants: ['sm','md','lg'] },

        // typography sizes
        { pattern: /^text-(xs|sm|base|lg|xl|2xl|3xl|4xl|5xl|6xl)$/, variants: ['sm','md','lg'] },

        // rounded / shadow / transform / opacity
        { pattern: /^rounded(-.*)?$/ },
        { pattern: /^shadow(-.*)?$/ },
        { pattern: /^(translate|rotate|scale|skew)-.*$/ },
        { pattern: /^opacity-\d{1,3}$/ },

        // helpers and display
        { pattern: /^(flex|inline-flex|items|justify|content|self|order)-.*$/ },
        { pattern: /^(hidden|block|inline-block|table|table-row|table-cell|sr-only)$/ },

        // border width / color combos (catch common dynamic combos)
        { pattern: /^border-(\d|t|b|l|r|x|y)$/, variants: ['sm','md','lg'] },
        { pattern: /^border-[a-z]+-(50|100|200|300|400|500|600|700|800|900)$/ },

        // gradient utilities
        { pattern: /^(bg-gradient-to-(t|tr|r|br|b|bl|l|tl)|from-[\w-]+|via-[\w-]+|to-[\w-]+)$/, variants: ['hover','md','lg'] },

        // small catch-all for typical utility ranges (kept controlled)
        { pattern: /^(-)?(m|p)(t|r|b|l|x|y)?-(?:0|1|2|3|4|5|6|8)$/, variants: ['sm','md','lg'] },
    ],
};