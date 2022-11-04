<script setup>
import {nextTick, onMounted, ref} from 'vue';

defineProps(['modelValue']);

defineEmits(['update:modelValue']);

const textarea = ref(null);

onMounted(() => {
    if (textarea.value.hasAttribute('autofocus')) {
        nextTick(() => {
            textarea.value.focus();
        })
    }
});
</script>

<template>
    <textarea :value="modelValue"
              @input="$emit('update:modelValue', $event.target.value)"
              ref="textarea"
              class="border-stone-600 focus:border-indigo-200 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md transition-colors ease-in-out duration-200"></textarea>
</template>
