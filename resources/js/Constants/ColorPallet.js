export const COLOR_PALLET = {
    'slate-400': '#94a3b8',
    'stone-400': '#a8a29e',
    'gray-900': '#111827',
    'red-500': '#ef4444',
    "red-600": "#e11923",
    'orange-400': '#fb923c',
    'orange-700': '#c2410c',
    'amber-400': '#fbbf24',
    'yellow-400': '#facc15',
    'lime-400': '#a3e635',
    'green-400': '#4ade80',
    'emerald-400': '#34d399',
    'teal-400': '#2dd4bf',
    'cyan-400': '#22d3ee',
    'cyan-600': '#0891b2',
    'sky-400': '#38bdf8',
    'sky-700': '#0369a1',
    'blue-400': '#60a5fa',
    'indigo-400': '#818cf8',
    'violet-400': '#a78bfa',
    'purple-400': '#c084fc',
    'fuchsia-400': '#e879f9',
    'pink-400': '#f472b6',
    'rose-400': '#fb7185',
}

export const COLOR_PALLET_LIST = () => {
    let array = [];

    for (const color in COLOR_PALLET) {
        array.push(COLOR_PALLET[color]);
    }

    return array;
}
