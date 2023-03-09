<script setup>
import {Link} from '@inertiajs/vue3';

defineProps({
    url: {
        type: String,
        required: true
    },
    active: {
        type: Boolean,
        default: false
    }
});
</script>
<template>
    <Link :href="url" class="py-2 flex flex-row items-center space-x-4 transition ease-in-out duration-200" :class="{'text-gray-900': active, 'text-gray-400 hover:text-gray-700': !active}">
        <div><slot name="icon" /></div>
        <div class="font-medium tracking-tight">
            <slot />
        </div>
    </Link>
</template>
