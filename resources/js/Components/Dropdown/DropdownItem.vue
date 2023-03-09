<script setup>
import {Link} from '@inertiajs/vue3';

defineProps({
    href: String,
    as: String,
    linkAs: String,
    linkMethod: {
        type: String,
        default: 'get'
    }
});

const classes = 'flex items-center p-sm w-full rounded-md text-gray-800 hover:bg-gray-100 transition ease-in-out duration-200';
</script>
<template>
    <button v-if="as === 'button'" type="button" :class="[classes, 'outline-none focus:outline-none']">
        <slot/>
    </button>

    <a v-else-if="as === 'a'" :href="href" :class="classes">
        <slot/>
    </a>

    <div v-else-if="as === 'div'" :class="classes">
        <slot/>
    </div>

    <Link v-else :href="href" :class="[classes]" :as="linkAs" :method="linkMethod">
        <slot/>
    </Link>
</template>
