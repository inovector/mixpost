<script setup>
defineProps({
    active: {
        type: Boolean,
        default: false,
    }
})
</script>
<template>
    <button
        :class="{'border-indigo-500': active, 'text-stone-700 hover:text-black':  !active}"
        class="flex items-center border-b-2 border-gray-200 pb-2 mr-3 mb-3 font-semibold transition-colors ease-in-out duration-200">
        <slot/>
    </button>
</template>
