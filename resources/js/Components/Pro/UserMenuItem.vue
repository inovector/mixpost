<script setup>
import ProLabel from "./ProLabel.vue";
import Flex from "../Layout/Flex.vue";
</script>
<template>
    <Flex class="relative items-center">
        <slot/>

        <span class="absolute right-0 top-0 mt-xs mr-xs">
            <ProLabel/>
        </span>
    </Flex>
</template>
