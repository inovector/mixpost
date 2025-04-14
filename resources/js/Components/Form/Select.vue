<script setup>
import {nextTick, onMounted, ref} from 'vue';

defineProps(['modelValue', 'error']);

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
    <select v-bind:value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            ref="select"
            :class="{'border-stone-600': !error, 'border-red-600': error}"
            class="w-full rounded-md focus:border-indigo-200 focus:ring-3 focus:ring-indigo-200/50 outline-hidden transition-colors ease-in-out duration-200">
        <slot/>
    </select>
</template>
