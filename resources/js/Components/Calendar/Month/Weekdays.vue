<script setup>
import {computed} from "vue";
import {clone} from "lodash";

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

const items = computed(() => {
    const days = clone(WEEKDAYS);

    return days.splice(props.weekStartsOn ? 0 : 6).concat(days);
})
</script>
<template>
    <div class="grid grid-cols-7">
        <div v-for="(item, index) in items" :key="index" class="p-sm border-t border-r last:border-r-0 border-gray-200 text-center font-semibold">
            <span class="hidden sm:block">{{ item.name }}</span>
            <span class="block sm:hidden">{{ item.name_short }}</span>
        </div>
    </div>
</template>
