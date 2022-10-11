<script setup>
defineProps({
    withPadding: {
        type: Boolean,
        default: true
    }
})
</script>
<template>
    <div class="bg-white border border-gray-100 rounded-lg">
        <div :class="{'p-6': withPadding}">
            <slot/>
        </div>
    </div>
</template>
