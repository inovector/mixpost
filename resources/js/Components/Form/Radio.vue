<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        default: false,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit("update:checked", val);
    },
});
</script>

<template>
    <input type="radio" :value="value" v-model="proxyChecked"
           class="rounded-full w-5 h-5 border-gray-200 text-indigo-500 shadow-sm focus:border-indigo-200 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:border-gray-100 disabled:cursor-not-allowed">
</template>
