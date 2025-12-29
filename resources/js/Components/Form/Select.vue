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

const select = ref(null)

onMounted(() => {
  if (select.value.hasAttribute('autofocus')) {
    nextTick(() => {
      select.value.focus()
    })
  }
})
</script>

<template>
  <select
    ref="select"
    :value="modelValue"
    :class="{ 'border-stone-600': !error, 'border-red-600': error }"
    class="w-full rounded-md focus:border-indigo-200 focus:ring-3 focus:ring-indigo-200/50 outline-hidden transition-colors ease-in-out duration-200"
    @change="$emit('update:modelValue', $event.target.value)"
  >
    <slot />
  </select>
</template>
