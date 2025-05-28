<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
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
    <input type="checkbox" :value="value" v-model="proxyChecked"
           class="rounded-md w-5 h-5 border-gray-200 text-indigo-500 shadow-xs cursor-pointer focus:border-default focus:ring-3 focus:ring-indigo-200/50 disabled:border-gray-100 disabled:cursor-not-allowed">
</template>
