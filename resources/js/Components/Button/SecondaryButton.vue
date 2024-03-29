<script setup>
import useButtonSize from "@/Composables/useButtonSize"
import CircleLoadingIcon from "@/Icons/CircleLoading.vue"

const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    size: {
        type: String,
        default: 'lg'
    },
    isLoading: {
        type: Boolean,
        default: false,
    }
});

const { sizeClass } = useButtonSize(props.size);
</script>

<template>
    <button :type="type" :class="sizeClass" class="relative inline-flex items-center bg-white text-black border border-indigo-800 rounded-md font-medium text-xs uppercase tracking-widest hover:text-indigo-500 hover:border-indigo-500 active:text-indigo-500 active:border-indigo-700 focus:border-indigo-700 focus:shadow-outline-indigo disabled:text-gray-500 disabled:cursor-not-allowed transition ease-in-out duration-200">
        <slot />
        <span v-if="isLoading" class="absolute left-0 top-0 flex justify-center items-center w-full h-full bg-white rounded-md">
             <CircleLoadingIcon class="animate-spin text-indigo-500"/>
        </span>
    </button>
</template>
