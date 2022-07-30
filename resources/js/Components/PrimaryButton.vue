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
    <button :type="type" :class="sizeClass" class="inline-flex items-center px-4 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-200">
        <slot />
    </button>
</template>
