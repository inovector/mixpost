<script setup>
import {computed, watch} from "vue";
import ProviderIcon from "@/Components/Account/ProviderIcon.vue";
import Twitter from "twitter-text";

const props = defineProps({
    provider: {
        type: String,
        required: true,
    },
    characterLimit: {
        type: Number,
        required: false,
    },
    text: {
        required: false,
        default: ''
    }
});

const emit = defineEmits(['reached']);

const characterUsed = computed(() => {
    return {
        'twitter': Twitter.getTweetLength(props.text),
        'facebook': props.text.length
    }[props.provider]
});

const remaining = computed(() => {
    return props.characterLimit - characterUsed.value;
});

watch(remaining, () => {
    emit('reached', remaining.value < 0);
});
</script>
<template>
    <div class="flex items-center justify-center">
        <div class="mr-1">
            <ProviderIcon :provider="provider"/>
        </div>
        <div :class="{'text-stone-800': remaining >= 0, 'text-red-500': remaining < 0}">{{ remaining }}</div>
    </div>
</template>
