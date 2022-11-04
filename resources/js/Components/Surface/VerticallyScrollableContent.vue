<script setup>
import {computed} from "vue";

const props = defineProps({
    maxHeight: {
        type: String,
        default: 'xl'
    }
})

const maxHeightClass = computed(()=> {
    return {
        'md' : 'max-h-64',
        'lg' : 'max-h-72',
        'xl' : 'max-h-96',
    }[props.maxHeight]
})
</script>
<template>
    <div :class="maxHeightClass" class="overflow-y-auto">
        <slot/>
    </div>
</template>
