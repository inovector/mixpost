<script setup>
defineProps({
    active: {
        type: Boolean,
        default: false,
    }
})
</script>
<template>
    <button
        type="button"
        role="tab"
        :aria-selected="active ? 'true' : 'false'"
        :class="{'border-indigo-500 text-indigo-500': active, 'hover:text-indigo-500':  !active}"
        class="flex items-center border-b-2 border-gray-200 pb-2 mr-5 last:mr-0 font-medium transition-colors ease-in-out duration-200">
         <span v-if="$slots.icon" class="inline-flex" :class="{'mr-xs': $slots.default}">
            <slot name="icon"/>
         </span>

        <slot/>
    </button>
</template>
