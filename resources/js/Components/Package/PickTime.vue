<script setup>
import {ref, onMounted, watch} from "vue";
import {format, isPast, addHours, parseISO} from "date-fns"
import DialogModal from "@/Components/Modal/DialogModal.vue"
import PrimaryButton from "@/Components/Button/PrimaryButton.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import FlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import '@css/overrideFlatPickr.css'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    date: {
        type: String,
        default: '',
    },
    time: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['close', 'update']);

const date = ref();
const time = ref();
const hasErrors = ref(false);

const timePicker = ref();

const setDateTime = () => {
    if (props.show) {
        date.value = props.date ? props.date : format(new Date(), 'Y-MM-dd');

        const nextHour = format(addHours(new Date(), 1), 'H');
        time.value = props.time ? props.time : (nextHour + ':00');
    }
}

const validate = () => {
    return new Promise(resolve => {
        // Prevent time value in the past
        const selected = new Date(parseISO(date.value + ' ' + time.value));

        if (isPast(selected)) {
            hasErrors.value = true;

            resolve(false);
            return;
        }

        hasErrors.value = false;

        resolve(true);
    });
}

onMounted(() => {
    setDateTime();
});

watch(() => props.show, () => {
    if (props.show) {
        setDateTime();
    }
});

watch([date, time], () => {
    validate();
});

const confirm = async () => {
    const hour = timePicker.value.querySelector('.flatpickr-hour').value;
    const minutes = timePicker.value.querySelector('.flatpickr-minute').value;

    time.value = hour + ':' + minutes; // we make sure we have the data that was entered manually (on keyup)

    const isValid = await validate();

    if (!isValid) {
        return;
    }

    emit('update', {
        date: date.value,
        time: time.value
    })

    close();
}

const close = () => {
    date.value = '';
    time.value = '';
    emit('close');
};

const configDatePicker = {
    inline: true,
    dateFormat: 'Y-m-d',
    minDate: "today",
    allowInput: false,
    monthSelectorType: 'static',
    yearSelectorType: 'static',
    static: true,
    prevArrow: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>',
    nextArrow: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>'
}

const configTimePicker = {
    inline: true,
    timeFormat: 'H:i',
    noCalendar: true,
    enableTime: true,
    time_24hr: true
}
</script>
<template>
    <DialogModal :show="show"
                        max-width="sm"
                        :closeable="true"
                        @close="close">
        <template #body>
            <div v-if="show" class="pickTime flex flex-col">
                <FlatPickr v-model="date" :config="configDatePicker"/>

                <div class="flex items-center justify-center mx-auto mt-lg">
                    <div class="mr-xs text-gray-400">Time</div>
                    <div class="w-auto" ref="timePicker">
                        <FlatPickr v-model="time" :config="configTimePicker"/>
                    </div>
                </div>
                <div v-if="hasErrors" class="mt-4 text-center text-red-500">The selected date and time is in the past
                </div>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="close" class="mr-xs">Cancel</SecondaryButton>
            <PrimaryButton @click="confirm" :disabled="hasErrors">Pick time</PrimaryButton>
        </template>
    </DialogModal>
</template>
