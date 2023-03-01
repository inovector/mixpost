<script setup>
import {startOfMonth, addMonths, parseISO, subMonths} from "date-fns";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import PureButton from "@/Components/Button/PureButton.vue"
import ChevronRightIcon from "@/Icons/ChevronRight.vue"
import ChevronLeftIcon from "@/Icons/ChevronLeft.vue"

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
    let newSelectedDate = startOfMonth(subMonths(props.selectedDate, 1));
    emit("dateSelected", newSelectedDate);
}

const selectCurrent = () => {
    const newSelectedDate = typeof props.currentDate === 'string' ? parseISO(props.currentDate) : props.currentDate;
    emit("dateSelected", newSelectedDate);
}

const selectNext = () => {
    let newSelectedDate = startOfMonth(addMonths(props.selectedDate, 1));
    emit("dateSelected", newSelectedDate);
}
</script>
<template>
    <div class="flex items-center">
        <SecondaryButton @click="selectCurrent" class="mr-xs">Today</SecondaryButton>

        <div class="flex items-center">
            <PureButton @click="selectPrevious" class="mr-xs">
                <ChevronLeftIcon/>
            </PureButton>
            <PureButton @click="selectNext">
                <ChevronRightIcon/>
            </PureButton>
        </div>
    </div>
</template>
