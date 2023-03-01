<script setup>
import {computed} from "vue";
import {addDays, getDate, startOfWeek} from "date-fns";
import {clone} from "lodash";
import {utcToZonedTime} from "date-fns-tz";

const props = defineProps({
    timeZone: {
        required: false,
        type: String,
        default: 'UTC'
    },
    weekStartsOn: {
        required: false,
        type: Number,
        default: 0
    },
    selectedDate: {
        type: Date,
        required: true,
    },
    scrolled: {
        type: Boolean,
        required: false,
        default: false
    }
});

const WEEKDAYS = [
    {
        name: 'Mon',
        name_short: 'M',
    },
    {
        name: 'Tue',
        name_short: 'T',
    },
    {
        name: 'Wed',
        name_short: 'W',
    },
    {
        name: 'Thu',
        name_short: 'T',
    },
    {
        name: 'Fri',
        name_short: 'F',
    },
    {
        name: 'Sat',
        name_short: 'S',
    },
    {
        name: 'Sun',
        name_short: 'S',
    },
];

const start = computed(() => {
    return startOfWeek(props.selectedDate, {
        weekStartsOn: props.weekStartsOn,
    })
});

const today = computed(() => {
    return getDate(utcToZonedTime(new Date().toISOString(), props.timeZone));
});

const items = computed(() => {
    const days = clone(WEEKDAYS);

    const items = days.splice(props.weekStartsOn ? 0 : 6).concat(days);

    return items.map((item, index) => {
        const date = index === 0 ? start.value : addDays(start.value, index);
        const monthDay = getDate(date);

        return Object.assign(item, {
            date: monthDay,
            isToday: today.value === monthDay
        })
    })
})
</script>
<template>
    <div class="flex flex-row sticky top-0 bg-white z-10">
        <div class="w-full grid grid-cols-week-time-sm md:grid-cols-week-time">
            <div></div>
            <div v-for="(item, index) in items"
                 :key="index"
                 :class="{'text-indigo-500': item.isToday, 'border-b-gray-200': scrolled, 'border-b-white': !scrolled}"
                 class="p-xs border-t border-b border-l border-gray-200 text-center font-semibold">
                <div class="text-base md:text-xl">{{ item.date }}</div>

                <span :class="{'text-gray-500': !item.isToday}">
                    <span class="hidden md:block">{{ item.name }}</span>
                    <span class="block md:hidden">{{ item.name_short }}</span>
                </span>
            </div>
        </div>
    </div>
</template>
