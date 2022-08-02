<script setup>
import {ref} from "vue";

defineProps({
    modelValue: {
        required: true,
    },
    placeholder: {
        type: String,
        default: ''
    }
});

defineEmits(['update:modelValue']);

const focused = ref(false);

function onFocus(e) {
   focused.value = true;
}

function onBlur() {
    focused.value = false;
}
</script>
<template>
    <div
        @input="$emit('update:modelValue', $event.target.innerText)"
        :class="{'border-indigo-200 ring ring-indigo-200 ring-opacity-50': focused}"
        class="border border-gray-200 rounded-md p-5 pb-2 transition-colors ease-in-out duration-200">
        <div @focus="onFocus" @blur="onBlur" contenteditable="true" class="focus:outline-none min-h-[150px]" :placeholder="$props.placeholder"></div>
        <slot/>
    </div>
</template>
<style>
[contenteditable=true]:empty:before{
    content: attr(placeholder);
    pointer-events: none;
    display: block; /* For Firefox */
    color: theme('colors.gray.400')
}
</style>
