<script setup>
import {ref, onMounted, onUnmounted, computed} from "vue";
import emitter from "@/Services/emitter";
import CheckIcon from "@/Icons/Check.vue"
import ExclamationIcon from "@/Icons/Exclamation.vue"
import XIcon from "@/Icons/X.vue"

const variant = ref('info');
const message = ref('');
const show = ref(false);

let showTimeout = null;

onMounted(() => {
    emitter.on('notify', e => open(e.variant, e.message));
});

onUnmounted(() => {
    emitter.off('notify');
});

const open = (variantName, messageText) => {
    if (showTimeout) {
        clearTimeout(showTimeout);
    }

    variant.value = variantName;
    message.value = messageText;
    show.value = true;

    showTimeout = setTimeout(() => {
        show.value = false;
    }, 2500);
}

const close = () => {
    if (showTimeout) {
        clearTimeout(showTimeout);
    }

    show.value = false
}

const variantIcon = computed(() => {
    return {
        'success': CheckIcon,
        'warning': ExclamationIcon,
        'error': ExclamationIcon,
        'info': ExclamationIcon,
    }[variant.value]
});

const variantColorClasses = computed(() => {
    return {
        'info': 'bg-cyan-100 text-cyan-600',
        'success': 'bg-lime-100 text-lime-600',
        'warning': 'bg-orange-100 text-orange-600',
        'error': 'bg-red-100 text-red-600',
    }[variant.value]
});
</script>
<template>
    <teleport to="body">
        <transition enter-active-class="transition ease-out duration-200"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95">
            <div v-show="show" class="absolute bottom-0 right-0 mr-xl mb-2xl flex px-lg py-md rounded-md bg-indigo-800 z-50"
                 aria-live="polite">
                <div class="flex items-center">
                    <div>
                        <div :class="variantColorClasses"
                             class="w-8 h-8 rounded-full flex items-center justify-center mr-sm">
                            <component :is="variantIcon"/>
                        </div>
                    </div>
                    <div class="text-gray-200">{{ message }}</div>
                    <button @click="close" class="ml-2xl">
                        <XIcon class="text-gray-200"/>
                    </button>
                </div>
            </div>
        </transition>
    </teleport>
</template>
