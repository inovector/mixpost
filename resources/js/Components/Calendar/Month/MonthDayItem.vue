<script setup>
import {computed} from "vue";
import {router} from "@inertiajs/vue3";
import {format} from "date-fns";
import {utcToZonedTime} from "date-fns-tz";
import CalendarPostItem from "@/Components/Calendar/CalendarPostItem.vue";
import PlusIcon from "@/Icons/Plus.vue"
import DisabledItemImg from "@img/calendar-disabled-item.svg"

const props = defineProps({
    day: {
        type: Object,
        required: true,
    },
    isToday: {
        type: Boolean,
        default: false,
    },
    timeZone: {
        required: false,
        type: String,
        default: 'UTC'
    },
})

const label = computed(() => {
    return format(new Date(`${props.day.date}T00:00:00`), 'd');
})

const style = computed(() => {
    if (!props.day.isDisabled) {
        return {};
    }

    return {
        backgroundImage: `url('${DisabledItemImg}')`
    }
})

const add = () => {
    const now = utcToZonedTime(new Date().toISOString(), props.timeZone);

    let scheduleAt = `${props.day.date} ${format(now, 'HH:mm')}`;

    router.visit(route('mixpost.posts.create', {schedule_at: scheduleAt}));
}
</script>
<template>
    <div
        class="relative min-h-[100px] overflow-hidden bg-white border-r border-b border-gray-200 group"
        :style="style"
    >
        <div class="absolute w-full top-0 left-0 mt-xs text-center">
            <span class="w-7 h-7 inline-flex items-center justify-center p-1 mr-xs rounded-full text-gray-700"
                  :class="{'bg-indigo-500 text-white': isToday,'text-gray-400': day.isDisabled}">{{ label }}</span>
        </div>

        <div
            v-if="!day.isDisabled"
            class="absolute mt-xs right-0 mr-sm opacity-0 group-hover:opacity-100 transition-opacity ease-in-out duration-300">
            <button @click="add" type="button"
                    class="text-gray-400 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                <PlusIcon/>
            </button>
        </div>

        <div v-if="day.posts.length" class="mt-xl pb-xl h-full overflow-hidden">
            <div class="relative p-0.5 md:p-sm overflow-y-auto mixpost-scroll-style h-full">
                <div class="flex flex-wrap space-y-xs w-full">
                    <template v-for="post in day.posts" :key="post.id">
                        <CalendarPostItem :item="post"/>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
