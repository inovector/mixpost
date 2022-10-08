<script setup>
import {nextTick, onMounted, ref} from 'vue';

defineProps(['modelValue']);

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
           class="border-stone-600 focus:border-indigo-200 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md transition-colors ease-in-out duration-200">
</template>
