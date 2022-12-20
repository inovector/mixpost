<script setup>
import {computed} from "vue";
import {format} from "date-fns";
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
})

const label = computed(() => {
    return format(new Date(props.day.date), 'd');
})

const style = computed(() => {
    if (!props.day.isDisabled) {
        return {};
    }

    return {
        backgroundImage: `url('${DisabledItemImg}')`
    }
})
</script>
<template>
    <div
        class="relative p-sm min-h-[100px] bg-white border-r border-b border-gray-200 group"
        :style="style"
    >
        <div class="absolute w-full top-0 left-0 mt-xs text-center">
            <span class="w-7 h-7 inline-flex items-center justify-center p-1 mr-xs rounded-full text-gray-700"
                  :class="{'bg-indigo-500 text-white': isToday,'text-gray-400': day.isDisabled}">{{ label }}</span>
        </div>
        <div
            v-if="!day.isDisabled"
            class="absolute mt-0 right-0 mr-sm opacity-0 group-hover:opacity-100 transition-opacity ease-in-out duration-300">
            <button type="button"
                    class="py-2 text-gray-400 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                <PlusIcon/>
            </button>
        </div>
    </div>
</template>
