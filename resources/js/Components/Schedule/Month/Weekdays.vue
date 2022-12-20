<script setup>
import {computed} from "vue";

const props = defineProps({
    weekStartsOn: {
        required: false,
        type: Number,
        default: 0
    }
});

const WEEKDAYS = [
    {
        name: 'Mon',
        name_short: 'M'
    },
    {
        name: 'Tue',
        name_short: 'T'
    },
    {
        name: 'Wed',
        name_short: 'W'
    },
    {
        name: 'Thu',
        name_short: 'T'
    },
    {
        name: 'Fri',
        name_short: 'F'
    },
    {
        name: 'Sat',
        name_short: 'S'
    },
    {
        name: 'Sun',
        name_short: 'S'
    },
];

const weekdays = computed(() => {
    return WEEKDAYS.splice(props.weekStartsOn ? 0 : 6).concat(WEEKDAYS);
})
</script>
<template>
    <ol class="day-of-week">
        <li v-for="weekday in weekdays" :key="weekday">
            <span class="hidden md:block">{{ weekday.name }}</span>
            <span class="block md:hidden">{{ weekday.name_short }}</span>
        </li>
    </ol>
</template>
<style>
.day-of-week {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.day-of-week > * {
    text-align: right;
    padding-right: 5px;
}
</style>
