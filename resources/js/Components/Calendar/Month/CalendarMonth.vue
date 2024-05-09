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
import DateIndicator from "@/Components/Calendar/Month/DateIndicator.vue";
import DateSelector from "@/Components/Calendar/Month/DateSelector.vue";
import Weekdays from "@/Components/Calendar/Month/Weekdays.vue";
import MonthDayItem from "@/Components/Calendar/Month/MonthDayItem.vue";

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
    posts: {
        required: false,
        type: Array,
        default: []
    }
});

const emit = defineEmits(['dateSelected'])

const selectedDate = ref(new Date(`${props.initialDate}T00:00:00`));

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
    return (getMonth(selectedDate.value) + 1).toString().padStart(2, '0');
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

    const previousMonthLastMondayDayOfMonth = getDate(subDays(new Date(`${currentMonthDays.value[0].date}T00:00:00`), visibleNumberOfDaysFromPreviousMonth))

    const previousMonth = subMonths(selectedDate.value, 1);

    return [...Array(visibleNumberOfDaysFromPreviousMonth)].map(
        (day, index) => {
            const date = new Date(`${getYear(previousMonth)}-${(getMonth(previousMonth) + 1).toString().padStart(2, '0')}-${(previousMonthLastMondayDayOfMonth + index).toString().padStart(2, '0')}T00:00:00`);

            return {
                date: format(date, 'yyyy-MM-dd'),
                isDisabled: isDatePast(date, props.timeZone),
                posts: getDayPosts(date)
            };
        }
    );
})

const currentMonthDays = computed(() => {
    return [...Array(numberOfDaysInMonth.value)].map((day, index) => {
        const date = new Date(`${year.value}-${month.value}-${(index + 1).toString().padStart(2, '0')}T00:00:00`);

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
        const date = new Date(`${getYear(nextMonth)}-${(getMonth(nextMonth) + 1).toString().padStart(2, '0')}-${(index + 1).toString().padStart(2, '0')}T00:00:00`);

        return {
            date: format(date, 'yyyy-MM-dd'),
            isDisabled: false,
            posts: getDayPosts(date)
        };
    });
})

const getWeekday = (date) => {
    return getDay(typeof date === 'string' ? new Date(`${date}T00:00:00`) : date);
}

const getDayPosts = (date) => {
    return props.posts.filter((post) => {
        return format(date, 'yyyy-MM-dd') === post.scheduled_at.date;
    });
}

const selectDate = (value) => {
    selectedDate.value = value;

    emit('dateSelected', value);
}
</script>
<template>
    <div class="bg-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between py-lg row-px">
            <div class="flex items-center space-x-xs mb-xs md:mb-0">
                <DateSelector
                    :current-date="today"
                    :selected-date="selectedDate"
                    @dateSelected="selectDate"
                />
                <DateIndicator :selected-date="selectedDate"/>
            </div>

            <slot name="header"/>
        </div>

        <Weekdays :weekStartsOn="weekStartsOn"/>

        <div class="calendar-month-height grid grid-cols-7 relative border-t border-t-gray-200">
            <MonthDayItem
                v-for="day in days"
                :key="day.date"
                :day="day"
                :isToday="day.date === today"
                :timeZone="timeZone"
            />
        </div>
    </div>
</template>
