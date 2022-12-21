<script setup>
import {computed, ref, provide} from "vue";
import {Head} from '@inertiajs/inertia-vue3';
import useSettings from "@/Composables/useSettings";
import CalendarMonth from "@/Components/Schedule/Month/CalendarMonth.vue";
import CalendarWeek from "@/Components/Schedule/Week/CalendarWeek.vue";
import ScheduleHeader from "@/Components/Schedule/ScheduleHeader.vue";

const {timeZone, weekStartsOn} = useSettings();

const type = ref('month');

const filter = ref({
    status: null,
    tags: [],
    accounts: []
})

const posts = ref([
    {
        date: '2022-12-21',
        time: '13:10',
        text: 'Lorem lipsum',
        image: null
    },
    {
        date: '2022-12-22',
        time: '15:14',
        text: 'Lorem lipsum',
        image: null
    }
]);

provide('calendarType', type);
provide('calendarFilter', filter);

const isCalendarMonthType = computed(() => {
    return type.value === 'month';
})

const isCalendarWeekType = computed(() => {
    return type.value === 'week';
})
</script>
<template>
    <Head title="Schedule"/>

    <CalendarMonth v-if="isCalendarMonthType" :weekStartsOn="weekStartsOn" :timeZone="timeZone" :posts="posts">
        <template #header>
            <ScheduleHeader/>
        </template>
    </CalendarMonth>

    <CalendarWeek v-if="isCalendarWeekType">
        <template #header>
            <ScheduleHeader/>
        </template>
    </CalendarWeek>
</template>
