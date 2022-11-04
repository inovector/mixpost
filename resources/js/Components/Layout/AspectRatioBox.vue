<script setup>
defineProps({
    ratio: {
        type: Number,
        default: 1
    }
})
</script>
<template>
    <template v-if="!ratio">
        <slot/>
    </template>

    <div v-if="ratio" class="block relative">
        <div class="absolute block top-0 left-0 bottom-0 right-0">
            <slot/>
        </div>
        <div class="block" :style="{paddingBottom: (1 / ratio) * 100 + '%'}"/>
    </div>
</template>
