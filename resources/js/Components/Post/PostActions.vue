<script setup>
import {computed, ref} from "vue";
import {format, parseISO} from "date-fns";
import {usePage} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";
import usePost from "@/Composables/usePost";
import useNotifications from "@/Composables/useNotifications";
import useSettings from "@/Composables/useSettings";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import PickTime from "@/Components/Package/PickTime.vue"
import CalendarIcon from "@/Icons/Calendar.vue"
import PaperAirplaneIcon from "@/Icons/PaperAirplane.vue"
import ChevronDownIcon from "@/Icons/ChevronDown.vue"
import XIcon from "@/Icons/X.vue"

const props = defineProps({
    form: {
        required: true,
        type: Object
    },
    canManage: {
        type: Boolean,
        default: false,
    }
});

const {postId, isReadOnly} = usePost();

const emit = defineEmits(['submit'])

const timePicker = ref(false);

const {timeFormat, weekStartsOn} = useSettings();

const scheduleTime = computed(() => {
    if (props.form.date && props.form.time) {
        return format(new Date(parseISO(props.form.date + ' ' + props.form.time)), `E, MMM do, 'at' ${timeFormat === 24 ? 'kk:mm' : 'h:mmaaa'}`, {
            weekStartsOn: weekStartsOn
        });
    }

    return null;
})

const clearScheduleTime = () => {
    props.form.date = '';
    props.form.time = '';
}

const {notify} = useNotifications();
const isLoading = ref(false);

const schedule = () => {
    isLoading.value = true;

    axios.post(route('mixpost.posts.schedule', {post: postId.value})).then((response) => {
        notify('success', response.data);

        Inertia.visit(route('mixpost.posts.index'));
    }).catch((error) => {
        console.log(error);
        notify('error', error.response.data);
    }).finally(() => {
        isLoading.value = false;
    })
}
</script>
<template>
    <div class="w-full flex items-center justify-end bg-stone-500 border-t border-gray-200 z-10">
        <div class="py-4 flex items-center space-x-xs row-px">
            <slot/>
            <div class="flex items-center" role="group">
                <SecondaryButton size="md"
                                 :class="{'!normal-case rounded-r-none border-r-indigo-800': scheduleTime}"
                                 @click="timePicker = true">
                    <CalendarIcon class="lg:mr-xs"/>
                    <span class="hidden lg:block">{{ scheduleTime ? scheduleTime : 'Pick time' }}</span>
                </SecondaryButton>

                <template v-if="scheduleTime">
                    <SecondaryButton size="md"
                                     @click="clearScheduleTime"
                                     v-tooltip="'Clear time'"
                                     class="rounded-l-none border-l-0 hover:text-red-500 !px-2">
                        <XIcon/>
                    </SecondaryButton>
                </template>

                <PickTime :show="timePicker"
                          :date="form.date"
                          :time="form.time"
                          @close="timePicker = false"
                          @update="form.date = $event.date; form.time = $event.time;"/>
            </div>

            <div v-if="!isReadOnly" class="flex items-center" role="group">
                <PrimaryButton @click="schedule" size="md"
                               :disabled="!postId || isLoading"
                               :isLoading="isLoading"
                               :class="{'rounded-r-none border-r-indigo-400': scheduleTime}">
                    <PaperAirplaneIcon class="mr-xs"/>
                    {{ scheduleTime ? 'Schedule' : 'Post now' }}
                </PrimaryButton>

                <PrimaryButton v-if="scheduleTime" size="md" :disabled="!postId"
                               class="rounded-l-none border-l-0 !px-2">
                    <ChevronDownIcon/>
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>
