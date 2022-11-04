<script setup>
import {provide, reactive} from "vue";

const context = reactive({
    reachedMaxCharacterLimit: {},
});

provide('postContext', context);
</script>
<template>
    <slot/>
</template>
