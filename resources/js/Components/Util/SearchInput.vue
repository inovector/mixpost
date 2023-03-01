<script setup>
import Input from "@/Components/Form/Input.vue";
import MagnifyingGlassIcon from "@/Icons/MagnifyingGlass.vue"
import XIcon from "@/Icons/X.vue"

defineProps({
    modelValue: {
        required: true,
        type: String,
    },
    placeholder: {
        type: String,
        default: 'Search by keyword'
    }
});

defineEmits(['update:modelValue']);
</script>
<template>
    <div class="relative">
        <Input type="text" :value="modelValue" @update:modelValue="$emit('update:modelValue', $event)" id="keyword"
               :placeholder="placeholder"
               class="w-full pl-11 pr-11"/>

        <label for="keyword" class="absolute top-0 left-0 ml-sm mt-xs">
            <MagnifyingGlassIcon class="text-stone-600"/>
        </label>

        <div v-if="modelValue" @click="$emit('update:modelValue', '')" tabindex="0" role="button"
             class="absolute top-0 right-0 mr-xs mt-2.5">
            <XIcon
                class="!w-5 !h-5 text-stone-600 hover:text-stone-800 transition-colors ease-in-out duration-200"/>
        </div>
    </div>
</template>
