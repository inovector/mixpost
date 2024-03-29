<script setup>
defineProps({
    flexColMobile: {
        type: Boolean,
        default: true,
    }
})
</script>
<template>
    <div>
        <div class="flex justify-between items-center">
            <div class="w-full flex sm:flex-row sm:items-center sm:justify-between"
                 :class="{'flex-col': flexColMobile}">
                <div v-if="$slots.title || $slots.description" class="flex flex-col justify-start w-full sm:mr-xs">
                    <div v-if="$slots.title"
                         class="font-medium">
                        <slot name="title"/>
                    </div>

                    <div v-if="$slots.description" class="text-gray-500">
                        <slot name="description"/>
                    </div>
                </div>

                <div v-if="$slots.default" class="w-full flex justify-start mt-xs sm:mt-0">
                    <slot/>
                </div>
            </div>
        </div>

        <div v-if="$slots.footer" class="mt-xs">
            <slot name="footer"/>
        </div>
    </div>
</template>
