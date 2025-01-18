<script setup>
import CreatedAt from "./CreatedAt.vue";
</script>
<template>
    <div class="flex flex-col">
        <div class="text-gray-700">
            <slot/>
        </div>

        <div v-if="$slots.created_at">
            <CreatedAt>
                <slot name="created_at"/>
            </CreatedAt>
        </div>
    </div>
</template>
