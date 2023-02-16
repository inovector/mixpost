<script setup>
import {provide, reactive} from "vue";

const context = reactive({
    textLimit: {},
});

provide('postContext', context);
</script>
<template>
    <slot/>
</template>
