<script setup>
import { nextTick, onMounted, ref } from 'vue'

defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  error: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])

const input = ref(null)

onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    nextTick(() => {
      input.value.focus()
    })
  }
})
</script>

<template>
  <input
    ref="input"
    :value="modelValue"
    :class="{ 'border-stone-600': !error, 'border-red-600': error }"
    class="w-full rounded-md focus:border-indigo-200 focus:ring-3 focus:ring-indigo-200/50 disabled:bg-gray-50 disabled:cursor-not-allowed outline-hidden transition-colors ease-in-out duration-200"
    @input="$emit('update:modelValue', $event.target.value)"
  />
</template>
