<script setup>
const props = defineProps({
  modelValue: {
    type: [Boolean, Number],
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const apply = () => {
  const value = typeof props.modelValue === 'boolean' ? !props.modelValue : props.modelValue ? 0 : 1
  emit('update:modelValue', value)
}
</script>
<template>
  <button
    type="button"
    role="checkbox"
    aria-checked="false"
    class="flex items-center focus:outline-hidden border-0 p-0 bg-none"
    @click="apply"
  >
    <slot />
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
