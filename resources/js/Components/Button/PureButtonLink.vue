<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true
    },
    method: {
        type: String,
        default: 'get'
    },
    as: {
        type: String,
        default: 'a'
    },
    type: {
        type: String,
        default: null
    }
});
</script>
<template>
    <Link :href="href" :methods="method" :as="as" :type="type" class="relative inline-flex items-center text-gray-400 hover:text-indigo-500 transition-colors ease-in-out duration-200">
        <slot/>
    </Link>
</template>
