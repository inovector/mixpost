<script setup>
import {computed, ref} from "vue";
import {format, parseISO} from "date-fns";
import MixpostPrimaryButton from "@/Components/PrimaryButton.vue"
import MixpostSecondaryButton from "@/Components/SecondaryButton.vue"
import MixpostPickTime from "@/Components/PickTime.vue"
import CalendarIcon from "@/Icons/Calendar.vue"
import PaperAirplaneIcon from "@/Icons/PaperAirplane.vue"
import ChevronDownIcon from "@/Icons/ChevronDown.vue"
import XIcon from "@/Icons/X.vue"

const props = defineProps({
    form: {
        required: true,
        type: Object
    }
});

const emit = defineEmits(['submit'])

const timePicker = ref(false);

const scheduleTime = computed(() => {
    if (props.form.date && props.form.time) {
        return format(new Date(parseISO(props.form.date + ' ' + props.form.time)), "E, MMM do, y 'at' kk:mm");
    }

    return null;
})

const clearScheduleTime = () => {
    props.form.date = '';
    props.form.time = '';
}

</script>
<template>
    <div class="w-full flex items-center justify-end bg-stone-500 border-t border-gray-200">
        <div class="py-4 flex items-center space-x-2 default-x-padding">
            <slot/>
            <div class="flex items-center" role="group">
                <MixpostSecondaryButton size="md"
                                        :class="{'!normal-case rounded-r-none border-r-indigo-800': scheduleTime}"
                                        @click="timePicker = true">
                    <CalendarIcon class="mr-2"/>
                    <span>{{ scheduleTime ? scheduleTime : 'Pick time' }}</span>
                </MixpostSecondaryButton>

                <template v-if="scheduleTime">
                    <MixpostSecondaryButton size="md"
                                            @click="clearScheduleTime"
                                            v-tooltip="'Clear time'"
                                            class="rounded-l-none border-l-0 hover:text-red-500 !px-2">
                        <XIcon/>
                    </MixpostSecondaryButton>
                </template>

                <MixpostPickTime :show="timePicker"
                                 :date="form.date"
                                 :time="form.time"
                                 @close="timePicker = false"
                                 @update="form.date = $event.date; form.time = $event.time;"/>
            </div>

            <div class="flex items-center" role="group">
                <MixpostPrimaryButton @click="$emit('submit', scheduleTime ? 'schedule' : 'now')" size="md"
                                      :class="{'rounded-r-none border-r-indigo-400': scheduleTime}">
                    <PaperAirplaneIcon class="mr-2"/>
                    {{ scheduleTime ? 'Schedule' : 'Post now' }}
                </MixpostPrimaryButton>

                <MixpostPrimaryButton v-if="scheduleTime" size="md"
                                      class="rounded-l-none border-l-0 !px-2">
                    <ChevronDownIcon/>
                </MixpostPrimaryButton>
            </div>
        </div>
    </div>
</template>
