<script setup>
import {computed, ref, provide, watch} from "vue";
import {Head} from '@inertiajs/vue3';
import {router} from "@inertiajs/vue3";
import {format} from "date-fns";
import {cloneDeep, pickBy, throttle} from "lodash";
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
    },
    filter: {
        type: Object,
        default: {}
    },
})

const {timeZone, timeFormat, weekStartsOn} = useSettings();

const type = ref(props.type);
const selectedDate = ref(props.selected_date)

const filter = ref({
    keyword: props.filter.keyword,
    status: props.filter.status,
    tags: props.filter.tags,
    accounts: props.filter.accounts
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
    const newSelectedDate = format(date, 'yyyy-MM-dd');

    selectedDate.value = newSelectedDate;

    fetchPostsThrottle(Object.assign({
        date: newSelectedDate
    }, pickBy(filter.value)));
}

watch(type, () => {
    fetchPosts(Object.assign({date: selectedDate.value, type: type.value}, pickBy(filter.value)));
})

watch(() => cloneDeep(filter.value), throttle(() => {
    fetchPosts(Object.assign({date: selectedDate.value}, pickBy(filter.value)))
}, 300))

const fetchPosts = (data) => {
    router.get(route('mixpost.calendar', data), {}, {
        preserveState: true,
        only: ['posts']
    });
}

const fetchPostsThrottle = throttle((data) => {
    fetchPosts(data);
}, 300)
</script>
<template>
    <Head title="Schedule"/>

    <CalendarMonth v-if="isCalendarMonthType"
                   :initialDate="selectedDate"
                   :weekStartsOn="weekStartsOn"
                   :timeZone="timeZone"
                   :posts="posts.data"
                   @dateSelected="dateSelected">
        <template #header>
            <CalendarToolbar/>
        </template>
    </CalendarMonth>

    <CalendarWeek v-if="isCalendarWeekType"
                  :initialDate="selectedDate"
                  :weekStartsOn="weekStartsOn"
                  :timeZone="timeZone"
                  :timeFormat="timeFormat"
                  :posts="posts.data"
                  @dateSelected="dateSelected">
        <template #header>
            <CalendarToolbar/>
        </template>
    </CalendarWeek>
</template>
