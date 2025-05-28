<script setup>
import ProLabel from "./ProLabel.vue";
</script>
<template>
    <div
       class="relative py-xs flex flex-row text-left items-center space-x-4 text-gray-400 hover:text-gray-700 transition ease-in-out duration-200">
        <span>
            <slot name="icon"/>
        </span>
        <span class="font-medium tracking-tight">
            <slot/>
        </span>

        <span class="absolute right-0 top-0 mt-2 -mr-5!">
            <ProLabel/>
        </span>
    </div>
</template>
