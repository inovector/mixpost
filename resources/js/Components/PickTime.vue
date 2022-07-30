<script setup>
import {ref, onMounted, watch} from "vue";
import {format, addHours} from "date-fns"
import MixpostDialogModal from "@/Components/DialogModal.vue"
import MixpostPrimaryButton from "@/Components/PrimaryButton.vue"
import MixpostSecondaryButton from "@/Components/SecondaryButton.vue"
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


const setDateTime = ()=> {
    if (props.show) {
        date.value = props.date ? props.date : format(new Date(), 'Y-MM-dd');

        const nextHour = format(addHours(new Date(), 1), 'H');
        time.value = props.time ? props.time : (nextHour + ':00');
    }
}

onMounted(() => {
    setDateTime();
});

watch(() => props.show, () => {
    if (props.show) {
        setDateTime();
    }
});

const confirm = () => {
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
    <MixpostDialogModal :show="show"
                        max-width="sm"
                        :closeable="true"
                        @close="close">
        <template #body>
            <div v-if="show" class="pickTime flex flex-col">
                <FlatPickr v-model="date" :config="configDatePicker"/>

                <div class="flex items-center justify-center mx-auto mt-6">
                    <div class="mr-2 text-gray-400">Time</div>
                    <div class="w-auto">
                        <FlatPickr v-model="time" :config="configTimePicker"/>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <MixpostSecondaryButton @click="close" class="mr-2">Cancel</MixpostSecondaryButton>
            <MixpostPrimaryButton @click="confirm">Pick time</MixpostPrimaryButton>
        </template>
    </MixpostDialogModal>
</template>
