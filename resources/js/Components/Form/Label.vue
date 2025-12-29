<script setup>
defineProps({
  value: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <label class="block font-medium text-gray-700 mb-xs">
    <span v-if="value">{{ value }}</span>
    <span v-else><slot /></span>
  </label>
</template>
