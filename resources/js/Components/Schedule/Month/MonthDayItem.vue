<script setup>
import {computed} from "vue";
import {format} from "date-fns";

const props = defineProps({
    day: {
        type: Object,
        required: true,
    },
    isToday: {
        type: Boolean,
        default: false,
    },
})

const label = computed(() => {
    return format(new Date(props.day.date), 'd');
})
</script>
<template>
    <li
        class="calendar-day"
        :class="{
      'calendar-day--not-current': day.isDisabled,
      'calendar-day--today': isToday,
    }"
    >
        <span>{{ label }}</span>
    </li>
</template>
<style>
.calendar-day {
    position: relative;
    min-height: 100px;
    font-size: 16px;
    background-color: #fff;
    color: #3e4e63;
    padding: 5px;
}

.calendar-day > span {
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    right: 2px;
    width: 20px;;
    height: 20px;;
}

.calendar-day--not-current {
    background-color: #e4e9f0;
    color: #b5c0cd;
}

.calendar-day--today {
    padding-top: 4px;
}

.calendar-day--today > span {
    color: #fff;
    border-radius: 9999px;
    background-color: #3e4e63;
}
</style>
