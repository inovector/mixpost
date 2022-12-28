<script setup>
defineProps({
    withPadding: {
        type: Boolean,
        default: true
    }
})
</script>
<template>
    <div class="bg-white border border-gray-100 rounded-lg">
        <div :class="{'p-lg': withPadding}">
            <div v-if="$slots.title"
                 class="text-lg font-bold text-black">
                <slot name="title"/>
            </div>

            <div v-if="$slots.description"
                 class="text-gray-500 mt-xs">
                <slot name="description"/>
            </div>

            <div :class="{'mt-lg': $slots.title || $slots.description}">
                <slot/>
            </div>
        </div>
    </div>
</template>
