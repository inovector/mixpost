<script setup>
import {computed} from "vue";
import {format} from "date-fns";

const props = defineProps({
    selectedDate: {
        type: Date,
        required: true
    }
})

const selectedMonth = computed(()=> {
    return format(props.selectedDate, 'MMMM yyyy');
});
</script>
<template>
    <div class="text-gray-700 font-semibold text-lg">{{ selectedMonth }}</div>
</template>

