<script setup>
import DocumentIcon from "@/Icons/Document.vue";
defineProps({
    href: {
        type: String,
        default: '#'
    }
})
</script>
<template>
    <div class="flex items-center">
        <span><DocumentIcon class="w-4! h-4! mr-xs"/></span>
        <span>Read the <a :href="href" target="_blank" class="link">documentation</a>.</span>
    </div>
</template>
