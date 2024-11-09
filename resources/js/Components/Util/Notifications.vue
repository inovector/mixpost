<script setup>
import {ref, onMounted, onUnmounted, computed, watch} from "vue";
import {Link} from '@inertiajs/vue3';
import {usePage} from "@inertiajs/vue3";
import emitter from "@/Services/emitter";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import CheckIcon from "@/Icons/Check.vue"
import ExclamationIcon from "@/Icons/Exclamation.vue"
import XIcon from "@/Icons/X.vue"

const variant = ref('info');
const message = ref('');
const button = ref(null)
const show = ref(false);

let showTimeout = null;

onMounted(() => {
    emitter.on('notify', e => open(e.variant, e.message, e.button));
});

onUnmounted(() => {
    emitter.off('notify');
});

const open = (variantName, messageText, buttonObject) => {
    if (showTimeout) {
        clearTimeout(showTimeout);
    }

    variant.value = variantName;
    message.value = messageText.replace(/\n/g, '<br />');

    if (buttonObject) {
        button.value = buttonObject;
    }

    show.value = true;

    showTimeout = setTimeout(() => {
        show.value = false;
        button.value = null;
    }, 5000);
}

const close = () => {
    if (showTimeout) {
        clearTimeout(showTimeout);
    }

    show.value = false
    button.value = null
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

// Flash Messages
const flash = computed(() => {
    return usePage().props.flash;
});

watch(() => flash.value, () => {
    if (flash.value.success) {
        open('success', flash.value.success);
    }

    if (flash.value.warning) {
        open('warning', flash.value.warning);
    }

    if (flash.value.error) {
        open('error', flash.value.error);
    }

    if (flash.value.info) {
        open('info', flash.value.info);
    }
}, {
    immediate: true,
    deep: true
})
</script>
<template>
    <teleport to="body">
        <transition enter-active-class="transition ease-out duration-200"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95">
            <div v-show="show"
                 class="absolute bottom-0 right-0 ml-sm md:ml-0 mr-sm md:mr-xl mb-2xl flex px-lg py-md rounded-md bg-indigo-800 shadow-mix z-50"
                 aria-live="polite">
                <div class="flex items-center">
                    <div>
                        <div :class="variantColorClasses"
                             class="w-8 h-8 rounded-full flex items-center justify-center mr-sm rtl:mr-0 rtl:ml-sm">
                            <component :is="variantIcon"/>
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-200" v-html="message"/>
                        <div v-if="button" class="mt-xs">
                            <Link :href="button.href">
                                <SecondaryButton @click="close">{{ button.name }}</SecondaryButton>
                            </Link>
                        </div>
                    </div>
                    <button @click="close" class="ml-2xl rtl:ml-0 rtl:mr-2xl hover:rotate-90 transition-transform ease-in-out duration-300">
                        <XIcon class="text-gray-200"/>
                    </button>
                </div>
            </div>
        </transition>
    </teleport>
</template>
