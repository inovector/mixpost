<script setup>
import {computed} from "vue";

const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    size: {
        type: String,
        default: 'lg'
    }
});

const sizeClass = computed(() => {
    return {
        'md': 'py-2',
        'lg': 'py-3',
    }[props.size];
});
</script>

<template>
    <button :type="type" :class="sizeClass" class="inline-flex items-center px-4 py-2 bg-white text-black border border-indigo-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:text-indigo-500 hover:border-indigo-500 active:text-indigo-500 active:border-indigo-700 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-200">
        <slot />
    </button>
</template>
