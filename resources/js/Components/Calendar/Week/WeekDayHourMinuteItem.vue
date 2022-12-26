<script setup>
import {computed} from "vue";
import {isDateTimePast} from "@/helpers";
import {addMinutes, format, parseISO} from "date-fns";
import CalendarPostItem from "@/Components/Calendar/CalendarPostItem.vue";
import PlusIcon from "@/Icons/Plus.vue"
import DisabledItemImg from "@img/calendar-disabled-item.svg"

const props = defineProps({
    dateSlot: {
        type: String,
        required: true,
    },
    timeSlot: {
        type: String,
        required: true,
    },
    minuteSlot: {
        type: Number,
        required: true,
    },
    timeFormat: {
        required: false,
        type: Number,
        default: 12
    },
    timeZone: {
        required: false,
        type: String,
        default: 'UTC'
    },
    posts: {
        type: Array,
        required: true,
    },
})

const isDisabled = computed(() => {
    const cellDateTimeMinute = addMinutes(parseISO(`${props.dateSlot} ${props.timeSlot}`), props.minuteSlot === 30 ? 60 : props.minuteSlot);

    return isDateTimePast(cellDateTimeMinute, props.timeZone);
})

const label = computed(() => {
    const cellDateTimeMinute = addMinutes(parseISO(`${props.dateSlot} ${props.timeSlot}`), props.minuteSlot);

    return format(cellDateTimeMinute, `${props.timeFormat === 12 ? 'h:mm aaa' : 'H:mm'}`)
})

const style = computed(() => {
    if (!isDisabled.value) {
        return {};
    }

    return {
        backgroundImage: `url('${DisabledItemImg}')`
    }
})
</script>
<template>
    <div
        class="relative min-h-[35px] group"
        :style="style"
    >
        <div
            v-if="!isDisabled"
            class="absolute top-0  right-0 mr-sm opacity-0 group-hover:opacity-100 transition-opacity ease-in-out duration-300">
            <button type="button"
                    class="flex items-center text-gray-400 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                <span class="mr-xs text-sm">{{ label }}</span>
                <PlusIcon/>
            </button>
        </div>

        <div v-if="posts.length" class="mt-xl pb-xl h-full overflow-hidden">
            <div class="relative p-sm overflow-y-auto mixpost-scroll-style h-full">
                <div class="flex flex-wrap space-y-xs w-full">
                    <template v-for="post in posts" :key="post.id">
                        <CalendarPostItem :item="post"/>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
