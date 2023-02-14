<script setup>
import {addDays, addMinutes, format, parseISO, startOfWeek} from "date-fns";
import {utcToZonedTime} from "date-fns-tz";
import {computed, ref} from "vue";
import {range, throttle} from "lodash";
import {convertTime24to12} from "@/helpers";
import DateIndicator from "@/Components/Calendar/Week/DateIndicator.vue";
import DateSelector from "@/Components/Calendar/Week/DateSelector.vue";
import Weekdays from "@/Components/Calendar/Week/Weekdays.vue";
import WeekDayTimeItem from "@/Components/Calendar/Week/WeekDayTimeItem.vue";

const props = defineProps({
    timeZone: {
        required: false,
        type: String,
        default: 'UTC'
    },
    initialDate: {
        required: false,
        type: [String, Date],
        default: (props) => {
            return format(utcToZonedTime(new Date().toISOString(), props.timeZone), 'yyyy-MM-dd')
        }
    },
    weekStartsOn: {
        required: false,
        type: Number,
        default: 0
    },
    timeFormat: {
        required: false,
        type: Number,
        default: 12
    },
    posts: {
        required: false,
        type: Array,
        default: []
    }
});

const emit = defineEmits(['dateSelected'])

const selectedDate = ref(new Date(props.initialDate));

const today = computed(() => {
    return format(utcToZonedTime(new Date().toISOString(), props.timeZone), 'yyyy-MM-dd')
});

const start = computed(() => {
    return startOfWeek(selectedDate.value, {
        weekStartsOn: props.weekStartsOn,
    })
});

const weekDays = computed(() => {
    return range(7).map((item) => {
        const date = item === 0 ? start.value : addDays(start.value, item);

        return format(date, 'yyyy-MM-dd')
    });
})

const dayTimes = computed(() => {
    let times = [];

    for (let i = 0; i < 24; i++) {
        const value = (i + ":00").padStart(5, "0");

        times.push({
            12: convertTime24to12(value, 'h aaa'),
            24: value,
        });
    }

    return times;
})

const minuteSlots = [
    {
        start: 0,
        end: 59
    }
];

const getPosts = (date, time, minuteSlot) => {
    return props.posts.filter((post) => {
        const startTime = format(addMinutes(parseISO(`${date} ${time}`), minuteSlot['start']), 'kk:mm');
        const endTime = format(addMinutes(parseISO(`${date} ${time}`), minuteSlot['end']), 'kk:mm');

        return date === post.scheduled_at.date && (post.scheduled_at.time >= startTime && post.scheduled_at.time <= endTime);
    });
}

const selectDate = (value) => {
    selectedDate.value = value;

    emit('dateSelected', value);
}

const scrolled = ref(false);

const onScroll = throttle(($event) => {
    scrolled.value = $event.target.scrollTop > 0
}, 100)
</script>
<template>
    <div class="bg-white ">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between p-lg">
            <div class="flex items-center space-x-xs mb-xs md:mb-0">
                <DateSelector
                    :currentDate="today"
                    :selectedDate="selectedDate"
                    @dateSelected="selectDate"
                />
                <DateIndicator :selectedDate="selectedDate" :weekStartsOn="weekStartsOn"/>
            </div>

            <slot name="header"/>
        </div>

        <div @scroll="onScroll" class="calendar-week-height-sm md:calendar-week-height-md xl:calendar-week-height overflow-y-auto">
            <Weekdays :timeZone="timeZone" :weekStartsOn="weekStartsOn" :selectedDate="selectedDate"
                      :scrolled="scrolled"/>

            <div class="w-full grid grid-cols-week-time-sm md:grid-cols-week-time">
                <template v-for="time in dayTimes">
                    <template v-for="(minuteSlot, minuteSlotIndex) in minuteSlots">
                        <div class="text-center text-gray-400 text-sm font-semibold">
                            {{ minuteSlotIndex === 0 ? time[timeFormat] : '' }}
                        </div>

                        <div v-for="(weekday, indexDay) in weekDays" :key="indexDay"
                             :class="{'!border-t-gray-100': minuteSlotIndex !== 0}"
                             class="grid grid-cols-1 border-l border-t border-gray-200 text-center bg-white">

                            <WeekDayTimeItem :dateSlot="weekday"
                                                   :timeSlot="time[24]"
                                                   :minuteSlot="minuteSlot"
                                                   :timeZone="timeZone"
                                                   :timeFormat="timeFormat"
                                                   :posts="getPosts(weekday, time[24], minuteSlot)"/>

                        </div>
                    </template>
                </template>
            </div>
        </div>
    </div>
</template>
