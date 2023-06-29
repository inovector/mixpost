<script setup>
import {Link} from "@inertiajs/vue3";
import {computed} from "vue";
import Alert from "../Util/Alert.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";

const props = defineProps({
    isConfigured: {
        type: Object,
        required: true,
    }
})

const any = computed(() => {
    return Object.keys(props.isConfigured).some((service) => {
        return !['tenor', 'unsplash'].includes(service) && props.isConfigured[service] !== true
    })
});
</script>
<template>
    <div v-if="any" class="mb-md">
        <Alert variant="warning" :closeable="false" class="mb-md">
            <p v-if="!isConfigured.facebook">You have not configured Facebook service.</p>
            <p v-if="!isConfigured.twitter">You have not configured Twitter service.</p>
            <p class="mt-xs italic">Click on the button below to configure the third-party services.</p>
        </Alert>

        <Link :href="route('mixpost.services.index')" class="inline-block">
            <PrimaryButton>Configure services</PrimaryButton>
        </Link>
    </div>
</template>
