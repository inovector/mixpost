<script setup>
import ExclamationIcon from "@/Icons/Exclamation.vue";

defineProps(['message'])
</script>
<template>
    <p v-if="message" class="inline-flex items-start text-red-500 leading-loose text-sm">
        <span class="mr-xs"><ExclamationIcon class="!w-5 !-5"/></span>
        <span>{{ message }}</span>
    </p>
</template>
