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
        type="button"
        role="tab"
        :aria-selected="active ? 'true' : 'false'"
        :class="{'border-indigo-500 text-indigo-500': active, 'hover:text-black':  !active}"
        class="flex items-center border-b-2 border-gray-200 pb-2 mr-5 last:mr-0 font-medium transition-colors ease-in-out duration-200">
        <slot/>
    </button>
</template>
