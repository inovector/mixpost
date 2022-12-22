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
    <div class="grid grid-cols-7">
        <div v-for="weekday in weekdays" :key="weekday" class="p-sm border-t border-r last:border-r-0 border-gray-200 text-center font-semibold">
            <span class="hidden md:block">{{ weekday.name }}</span>
            <span class="block md:hidden">{{ weekday.name_short }}</span>
        </div>
    </div>
</template>
