<script setup>
import {computed} from "vue";

const props = defineProps({
    value: {
        type: String,
        required: true
    }
})

const classNames = computed(() => {
    return {
        'DRAFT': 'bg-gray-500',
        'PUBLISHED': 'bg-lime-500',
        'SCHEDULED': 'bg-cyan-500',
    }[props.value]
})

const name = computed(() => {
    return {
        'DRAFT': 'Draft',
        'PUBLISHED': 'Published',
        'SCHEDULED': 'Scheduled',
    }[props.value]
})
</script>
<template>
    <div class="flex items-center">
        <div :class="[classNames]" class="w-4 h-4 mr-2 rounded-full"></div>
        <div>{{ name }}</div>
    </div>
</template>
