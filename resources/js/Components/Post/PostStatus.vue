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
        'PUBLISHING': 'bg-violet-500',
        'SCHEDULED': 'bg-cyan-500',
        'ERROR': 'bg-red-500',
    }[props.value]
})

const name = computed(() => {
    return {
        'DRAFT': 'Draft',
        'PUBLISHED': 'Published',
        'PUBLISHING': 'Publishing',
        'SCHEDULED': 'Scheduled',
        'ERROR': 'Error',
    }[props.value]
})
</script>
<template>
    <div class="flex items-center">
        <div :class="[classNames]" class="w-4 h-4 mr-xs rounded-full"></div>
        <div>{{ name }}</div>
    </div>
</template>
