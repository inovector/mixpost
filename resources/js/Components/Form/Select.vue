<script setup>
import {nextTick, onMounted, ref} from 'vue';

defineProps(['modelValue']);

defineEmits(['update:modelValue']);

const select = ref(null);

onMounted(() => {
    if (select.value.hasAttribute('autofocus')) {
        nextTick(() => {
            select.value.focus();
        })
    }
});
</script>

<template>
    <select :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            ref="select"
            class="w-full border-stone-600 rounded-md focus:border-indigo-200 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 outline-none transition-colors ease-in-out duration-200">
        <slot/>
    </select>
</template>
