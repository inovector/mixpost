<script setup>
import {computed, ref, provide, watch} from "vue";
import {Head} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {format} from "date-fns";
import useSettings from "@/Composables/useSettings";
import CalendarMonth from "@/Components/Calendar/Month/CalendarMonth.vue";
import CalendarWeek from "@/Components/Calendar/Week/CalendarWeek.vue";
import CalendarToolbar from "@/Components/Calendar/CalendarToolbar.vue";

const props = defineProps({
    posts: {
        required: true,
        type: Object,
    },
    type: {
        required: true,
        type: String
    },
    selected_date: {
        required: true,
        type: [String, Date]
    }
})

const {timeZone, weekStartsOn} = useSettings();

const type = ref(props.type);

const filter = ref({
    status: null,
    tags: [],
    accounts: []
});

provide('calendarType', type);
provide('calendarFilter', filter);

const isCalendarMonthType = computed(() => {
    return type.value === 'month';
})

const isCalendarWeekType = computed(() => {
    return type.value === 'week';
})

const dateSelected = (date) => {
    fetchPosts({date: format(date, 'yyyy-MM-dd')});
}

watch(type, () => {
    fetchPosts({date: props.selected_date, type: type.value});
})

const fetchPosts = (data) => {
    Inertia.get(route('mixpost.calendar', data), {}, {
        preserveState: true,
        only: ['posts']
    });
}
</script>
<template>
    <Head title="Schedule"/>

    <CalendarMonth v-if="isCalendarMonthType"
                   :initialDate="selected_date"
                   :weekStartsOn="weekStartsOn"
                   :timeZone="timeZone"
                   :posts="posts.data"
                   @dateSelected="dateSelected">
        <template #header>
            <CalendarToolbar/>
        </template>
    </CalendarMonth>

    <CalendarWeek v-if="isCalendarWeekType">
        <template #header>
            <CalendarToolbar/>
        </template>
    </CalendarWeek>
</template>
