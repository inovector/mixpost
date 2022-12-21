<script setup>
import {computed, ref} from "vue";
import {
    format,
    getDaysInMonth,
    subMonths,
    subDays,
    getDate,
    getYear,
    getMonth,
    lastDayOfMonth,
    addMonths,
    getDay
} from "date-fns"
import {utcToZonedTime} from "date-fns-tz";
import {isDatePast} from "@/helpers";
import DateIndicator from "@/Components/Schedule/Month/DateIndicator.vue";
import DateSelector from "@/Components/Schedule/Month/DateSelector.vue";
import Weekdays from "@/Components/Schedule/Month/Weekdays.vue";
import MonthDayItem from "@/Components/Schedule/Month/MonthDayItem.vue";

const props = defineProps({
    timeZone: {
        required: false,
        type: String,
        default: 'UTC'
    },
    initialDate: {
        required: false,
        type: String,
        default: (props) => {
            return format(utcToZonedTime(new Date().toISOString(), props.timeZone), 'yyyy-MM-dd')
        }
    },
    weekStartsOn: {
        required: false,
        type: Number,
        default: 0
    },
    posts: {
        required: false,
        type: Array,
        default: []
    }
});

const selectedDate = ref(new Date(props.initialDate));

const days = computed(() => {
    return [
        ...previousMonthDays.value,
        ...currentMonthDays.value,
        ...nextMonthDays.value
    ]
});

const today = computed(() => {
    return format(utcToZonedTime(new Date().toISOString(), props.timeZone), 'yyyy-MM-dd')
});

const month = computed(() => {
    return getMonth(selectedDate.value) + 1;
})

const year = computed(() => {
    return getYear(selectedDate.value)
})

const numberOfDaysInMonth = computed(() => {
    return getDaysInMonth(selectedDate.value);
});

const previousMonthDays = computed(() => {
    const firstDayOfTheMonthWeekday = getWeekday(
        currentMonthDays.value[0].date
    );

    const visibleNumberOfDaysFromPreviousMonth = firstDayOfTheMonthWeekday
        ? firstDayOfTheMonthWeekday - props.weekStartsOn
        : props.weekStartsOn ? 6 : 0;

    const previousMonthLastMondayDayOfMonth = getDate(subDays(new Date(currentMonthDays.value[0].date), visibleNumberOfDaysFromPreviousMonth))

    const previousMonth = subMonths(selectedDate.value, 1);

    return [...Array(visibleNumberOfDaysFromPreviousMonth)].map(
        (day, index) => {
            const date = new Date(`${getYear(previousMonth)}-${getMonth(previousMonth) + 1}-${previousMonthLastMondayDayOfMonth + index}`);

            return {
                date: format(date, 'yyyy-MM-dd'),
                isDisabled: isDatePast(date, props.timeZone),
                posts: []
            };
        }
    );
})

const currentMonthDays = computed(() => {
    return [...Array(numberOfDaysInMonth.value)].map((day, index) => {
        const date = new Date(`${year.value}-${month.value}-${index + 1}`);

        return {
            date: format(date, 'yyyy-MM-dd'),
            isDisabled: isDatePast(date, props.timeZone),
            posts: getDayPosts(date)
        };
    });
})

const nextMonthDays = computed(() => {
    const lastDayOfTheMonthWeekday = getWeekday(lastDayOfMonth(selectedDate.value));

    const visibleNumberOfDaysFromNextMonth = lastDayOfTheMonthWeekday
        ? (props.weekStartsOn ? 7 : 6) - lastDayOfTheMonthWeekday
        : lastDayOfTheMonthWeekday;

    const nextMonth = addMonths(selectedDate.value, 1);

    return [...Array(visibleNumberOfDaysFromNextMonth)].map((day, index) => {
        return {
            date: format(new Date(`${getYear(nextMonth)}-${getMonth(nextMonth) + 1}-${index + 1}`), 'yyyy-MM-dd'),
            isDisabled: false,
            posts: []
        };
    });
})

const getWeekday = (date) => {
    return getDay(typeof date === 'string' ? new Date(date) : date);
}

const getDayPosts = (date) => {
    return props.posts.filter((post) => {
        return format(date, 'yyyy-MM-dd') === post.date;
    });
}

const selectDate = (value) => {
    selectedDate.value = value;
}
</script>
<template>
    <div class="bg-white">
        <div class="flex items-center justify-between p-lg">
            <div class="flex items-center space-x-xs">
                <DateSelector
                    :current-date="today"
                    :selected-date="selectedDate"
                    @dateSelected="selectDate"
                />
                <DateIndicator
                    :selected-date="selectedDate"
                    class="calendar-month-header-selected-month"
                />
            </div>
            <slot name="header"/>
        </div>

        <Weekdays :weekStartsOn="weekStartsOn"/>

        <div class="grid grid-cols-7 h-screen relative border-t border-t-gray-200">
            <MonthDayItem
                v-for="day in days"
                :key="day.date"
                :day="day"
                :is-today="day.date === today"
            />
        </div>
    </div>
</template>
