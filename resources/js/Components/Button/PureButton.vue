<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    }
});
</script>
<template>
    <button :type="type" class="relative inline-flex items-center text-gray-400 hover:text-indigo-500 transition-colors ease-in-out duration-200">
        <slot/>
    </button>
</template>
