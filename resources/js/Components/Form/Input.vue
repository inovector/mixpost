<script setup>
import {nextTick, onMounted, ref} from 'vue';

defineProps(['modelValue', 'error']);

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        nextTick(() => {
            input.value.focus();
        })
    }
});
</script>

<template>
    <input :value="modelValue"
           @input="$emit('update:modelValue', $event.target.value)"
           ref="input"
           :class="{'border-stone-600': !error, 'border-red-600': error}"
           class="w-full rounded-md focus:border-indigo-200 focus:ring-3 focus:ring-indigo-200/50 disabled:bg-gray-50 disabled:cursor-not-allowed outline-hidden transition-colors ease-in-out duration-200">
</template>
