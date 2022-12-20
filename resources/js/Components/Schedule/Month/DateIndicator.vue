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
    <div class="calendar-date-indicator">{{ selectedMonth }}</div>
</template>
<style>
.calendar-date-indicator {
    font-size: 24px;
    font-weight: 600;
    color: gray
}
</style>
