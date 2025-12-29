<script setup>
import { computed, ref } from 'vue'
import PureButton from '../Button/PureButton.vue'
import Input from './Input.vue'
import Eye from '../../Icons/Eye.vue'
import EyeSlash from '../../Icons/EyeSlash.vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    required: true
  },
  error: {
    type: Boolean,
    default: false
  },
  id: {
    type: String,
    default: ''
  },
  autocomplete: {
    type: String,
    default: 'off'
  },
  placeholder: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  revealText: {
    type: String,
    default: 'Reveal'
  },
  hideText: {
    type: String,
    default: 'Hide'
  }
})

defineEmits(['update:modelValue'])

const type = ref('password')

const isVisible = computed(() => {
  return type.value === 'text'
})

const text = computed(() => {
  return isVisible.value ? props.hideText : props.revealText
})

const toggleType = () => {
  type.value = type.value === 'text' ? 'password' : 'text'
}
</script>
<template>
  <div class="flex relative items-center w-full">
    <Input
      :id="id"
      :type="type"
      :value="modelValue"
      :autocomplete="autocomplete"
      :placeholder="placeholder"
      :error="error"
      :required="required"
      class="pr-2xl"
      @update:model-value="$emit('update:modelValue', $event)"
    />

    <div class="absolute right-0 flex items-center mr-xs">
      <PureButton v-tooltip="text" @click="toggleType">
        <template #icon>
          <Eye v-if="!isVisible" />
          <EyeSlash v-else />
        </template>
      </PureButton>
    </div>
  </div>
</template>
