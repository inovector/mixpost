<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import {computed} from "vue";

const props = defineProps({
    href: {
        type: String,
        required: true
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
    <Link :href="href" :class="sizeClass" class="inline-flex items-center px-4 py-2 bg-indigo-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo transition ease-in-out duration-150">
        <slot />
    </Link>
</template>
