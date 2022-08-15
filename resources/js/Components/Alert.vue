<script setup>
import {ref, computed} from "vue";
import CheckIcon from "@/Icons/Check.vue"
import ExclamationIcon from "@/Icons/Exclamation.vue"
import XIcon from "@/Icons/X.vue"

const props = defineProps({
    variant: {
        type: String,
        default: 'info'
    },
    closeable: {
        type: Boolean,
        default: true,
    },
})

const show = ref(true);

const close = () => {
    show.value = false
}

const variantIcon = computed(() => {
    return {
        'success': CheckIcon,
        'warning': ExclamationIcon,
        'error': ExclamationIcon,
        'info': ExclamationIcon,
    }[props.variant]
});

const variantColorClasses = computed(() => {
    return {
        'info': 'bg-cyan-100 text-cyan-600',
        'success': 'bg-green-100 text-green-600',
        'warning': 'bg-orange-100 text-orange-600',
        'error': 'bg-red-100 text-red-600',
    }[props.variant]
});
</script>
<template>
    <div v-if="show" class="flex px-5 py-4 rounded-md bg-indigo-800">
        <div class="flex items-center">
            <div>
                <div :class="variantColorClasses"
                     class="w-8 h-8 rounded-full flex items-center justify-center mr-3">
                    <component :is="variantIcon"/>
                </div>
            </div>
            <div class="text-gray-200">
                <slot/>
            </div>
            <template v-if="$props.closeable">
                <button @click="close" class="ml-10">
                    <XIcon class="text-gray-200"/>
                </button>
            </template>
        </div>
    </div>
</template>
