<script setup>
import {addMonths, parseISO, subMonths} from "date-fns";

const props = defineProps({
    currentDate: {
        type: [String, Date],
        required: true,
    },
    selectedDate: {
        type: Date,
        required: true,
    },
})

const emit = defineEmits(['dateSelected']);

const selectPrevious = () => {
    let newSelectedDate = subMonths(props.selectedDate, 1);
    emit("dateSelected", newSelectedDate);
}

const selectCurrent = () => {
    const newSelectedDate = typeof props.currentDate === 'string' ? parseISO(props.currentDate) : props.currentDate;
    emit("dateSelected", newSelectedDate);
}

const selectNext = () => {
    let newSelectedDate = addMonths(props.selectedDate, 1);
    emit("dateSelected", newSelectedDate);
}
</script>
<template>
    <div class="calendar-date-selector">
        <span @click="selectPrevious"> &lt; </span>
        <span @click="selectCurrent">Today</span>
        <span @click="selectNext">></span>
    </div>
</template>
<style>
.calendar-date-selector {
    display: flex;
    justify-content: space-between;
    width: 80px;
    color: gray;
}

.calendar-date-selector > * {
    cursor: pointer;
    user-select: none;
}
</style>
