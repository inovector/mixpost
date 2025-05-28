<script setup>
import {ref} from "vue";

const props = defineProps(['modelValue']);

const emit = defineEmits(['update:modelValue']);

const state = ref(props.value);

const apply = () => {
    let value = typeof props.modelValue === "boolean" ? !props.modelValue : props.modelValue ? 0 : 1;
    emit('update:modelValue', value)
}
</script>
<template>
    <button
        @click="apply"
        type="button"
        role="checkbox"
        aria-checked="false"
        class="flex items-center focus:outline-hidden border-0 p-0 bg-none"
    >
        <slot/>
        <span
            :class="{ 'justify-start': !modelValue, 'justify-end': modelValue }"
            class="inline-flex items-center px-1 border border-stone-600 h-6 w-10 rounded-full focus:outline-hidden"
        >
          <span
              :class="{ 'bg-indigo-500': modelValue, 'bg-gray-500': !modelValue }"
              class="block rounded-full w-3 h-3"
          />
        </span>
    </button>
</template>
