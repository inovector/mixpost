<script setup>
defineProps({
    title: {
        required: true,
        type: String
    }
})
</script>
<template>
    <div class="flex items-center justify-between sm:px-6 lg:px-8 mb-10">
        <h1 class="font-bold text-xl">
            {{ $props.title }}
        </h1>
        <slot />
    </div>
</template>
