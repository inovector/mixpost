<script setup>
import {computed} from "vue";
import {format, startOfWeek, endOfWeek} from "date-fns";

const props = defineProps({
    selectedDate: {
        type: Date,
        required: true
    },
    weekStartsOn: {
        required: false,
        type: Number,
        default: 0
    },
})

const label = computed(() => {
    const start = startOfWeek(props.selectedDate, {
        weekStartsOn: props.weekStartsOn,
    });

    const end = endOfWeek(props.selectedDate, {
        weekStartsOn: props.weekStartsOn,
    });

    return `${format(start, 'MMM do')} - ${format(end, 'MMM do')}`
});
</script>
<template>
    <div class="text-gray-700 font-semibold text-lg">{{ label }}</div>
</template>

