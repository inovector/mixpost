<script setup>
defineProps({
    hoverable: {
        type: Boolean,
        default: false
    }
})
</script>
<template>
    <tr :class="{'bg-white hover:bg-gray-50 transition-colors ease-in-out duration-200': hoverable}">
        <slot/>
    </tr>
</template>
