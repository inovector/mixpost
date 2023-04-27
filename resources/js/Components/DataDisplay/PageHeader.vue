<script setup>
defineProps({
    title: {
        required: true,
        type: String
    }
})
</script>
<template>
    <div class="row-px mb-2xl">
        <div class="flex items-start justify-between">
            <h1 class="font-semibold text-xl">
                {{ $props.title }}
            </h1>
            <slot/>
        </div>
        <div v-if="$slots.description" class="mt-xs text-lg">
            <slot name="description"/>
        </div>
    </div>
</template>
