<script setup>
defineProps({
    title: {
        required: true,
        type: String
    }
})
</script>
<template>
    <div class="default-x-padding mb-10">
        <div class="flex items-center justify-between">
            <h1 class="font-bold text-xl">
                {{ $props.title }}
            </h1>
            <slot/>
        </div>
        <div v-if="$slots.description" class="mt-2 text-lg">
            <slot name="description"/>
        </div>
    </div>
</template>
