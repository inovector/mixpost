<script setup>
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
    href: String,
    as: String,
    linkAs: String,
});

const classes = 'flex items-center py-2 px-3 w-full rounded-md text-gray-800 hover:bg-gray-100 transition ease-in-out duration-200';
</script>
<template>
    <button v-if="as === 'button'" type="button" :class="[classes, 'outline-none focus:outline-none']">
        <slot />
    </button>

    <a v-else-if="as === 'a'" :href="href" :class="classes">
        <slot />
    </a>

    <Link v-else :href="href" :class="[classes]" :as="linkAs">
        <slot />
    </Link>
</template>
