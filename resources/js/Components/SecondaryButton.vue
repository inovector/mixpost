<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-white text-black border border-indigo-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:text-indigo-500 hover:border-indigo-500 active:text-indigo-500 active:border-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-200">
        <slot />
    </button>
</template>
