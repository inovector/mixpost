<script setup>
import {computed} from "vue";
import useProviderClassesColor from "@/Composables/useProviderClassesColor";
import TwitterIcon from "@/Icons/Twitter.vue";
import FacebookIcon from "@/Icons/Facebook.vue";
import MastodonIcon from "@/Icons/Mastodon.vue";

const props = defineProps({
    provider: {
        type: String,
        required: true
    }
})

const providers = {
    'twitter': TwitterIcon,
    'facebook': FacebookIcon,
    'facebook_page': FacebookIcon,
    'facebook_group': FacebookIcon,
    'mastodon': MastodonIcon,
};

const component = computed(() => {
    return providers[props.provider];
});

const {textClasses} = useProviderClassesColor(props.provider);
</script>
<template>
    <component :is="component" :class="textClasses"/>
</template>
