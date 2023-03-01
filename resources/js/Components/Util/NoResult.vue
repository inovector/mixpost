<script setup>
import ExclamationIcon from "@/Icons/Exclamation.vue"

</script>
<template>
    <div class="flex items-center">
        <div class="w-8 h-8 bg-cyan-100 text-cyan-600 rounded-full flex items-center justify-center mr-xs">
            <ExclamationIcon/>
        </div>
        <div>
            <template v-if="$slots.default"><slot/></template>
            <template v-if="!$slots.default">There are no results.</template>
        </div>
    </div>
</template>
