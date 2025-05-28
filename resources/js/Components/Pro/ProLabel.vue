<script setup>
import LockClosed from "../../Icons/LockClosed.vue";
import LockOpen from "../../Icons/LockOpen.vue";

defineProps({
    withTitle: {
        type: Boolean,
        default: true
    },
    paddingClass: {
        type: String,
        default: 'py-[1px] px-1'
    },
    name: {
        type: String,
        default: 'Pro'
    },
    icon: {
        type: String,
        default: 'lock-closed'
    }
})
</script>
<template>
    <span
        :class="paddingClass"
        class="relative inline-flex items-center bg-indigo-500 bg-linear-to-r from-purple-100 to-indigo-100 rounded-md text-[11px] text-gray-500">
            <span :class="{'sm:mr-[3px]': withTitle}" class="inline-flex">
                <template v-if="icon === 'lock-closed'">
                      <LockClosed class="w-3! h-3!"/>
                </template>

                <template v-if="icon === 'lock-open'">
                      <LockOpen class="w-3! h-3!"/>
                </template>
            </span>

        <template v-if="withTitle">
            <span class="inline-flex items-center">{{ name }}</span>
        </template>
    </span>
</template>
