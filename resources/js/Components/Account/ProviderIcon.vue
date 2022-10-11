<script setup>
import {computed} from "vue";
import TwitterIcon from "@/Icons/Twitter.vue";
import FacebookIcon from "@/Icons/Facebook.vue";

const props = defineProps({
    provider: {
        type: String,
        required: true
    }
})

const providers = {
    'twitter': TwitterIcon,
    'facebook': FacebookIcon,
};

const component = computed(() => {
    return providers[props.provider];
});
</script>
<template>
    <component :is="component" :class="['text-' + props.provider]"/>
</template>
