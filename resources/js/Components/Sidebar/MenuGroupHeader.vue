<script setup>
import {Link} from '@inertiajs/vue3';
defineProps({
    createUrl: {
        type: String,
        default: null
    }
});
</script>
<template>
    <div class="flex justify-between items-center">
        <div class="font-semibold text-black"><slot/></div>
        <template v-if="createUrl">
            <Link :href="createUrl" class="py-2 text-gray-400 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                <slot name="icon" />
            </Link>
        </template>
    </div>
</template>
