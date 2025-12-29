<script setup>
import { nextTick, onMounted, ref } from 'vue'

defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  error: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])

const textarea = ref(null)

onMounted(() => {
  if (textarea.value.hasAttribute('autofocus')) {
    nextTick(() => {
      textarea.value.focus()
    })
  }
})
</script>

<template>
  <textarea
    ref="textarea"
    :value="modelValue"
    :class="{ 'border-stone-600': !error, 'border-red-600': error }"
    class="w-full rounded-md focus:border-indigo-200 focus:ring-3 focus:ring-indigo-200/50 outline-hidden transition-colors ease-in-out duration-200"
    @input="$emit('update:modelValue', $event.target.value)"
  ></textarea>
</template>
