<script setup>
import {Link} from '@inertiajs/vue3';
import {computed} from "vue";

const props = defineProps({
    href: String,
    as: String,
    linkAs: String,
    linkMethod: {
        type: String,
        default: 'get'
    },
    isActive: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: 'sm',
    }
});

const sizeClass = computed(() => {
    return {
        'xs': 'p-xs',
        'sm': 'p-sm',
        'md': 'p-md',
    }[props.size];
});

const classes = computed(() => {
    return `flex items-center text-left w-full first:rounded-t-md last:rounded-b-md text-gray-800 transition ease-in-out duration-200 ${props.isActive ? 'bg-indigo-50' : 'hover:bg-gray-100'}`;
});

const iconClass = 'mr-xs rtl:mr-0 rtl:ml-xs';
</script>
<template>
    <button v-if="as === 'button'" type="button" :class="[classes, sizeClass, 'outline-hidden focus:outline-hidden']">
        <span v-if="$slots.icon" :class="iconClass">
            <slot name="icon"/>
        </span>
        <slot/>
    </button>

    <a v-else-if="as === 'a'" :href="href" :class="[classes, sizeClass]">
        <span v-if="$slots.icon" :class="iconClass">
            <slot name="icon"/>
        </span>
        <slot/>
    </a>

    <div v-else-if="as === 'div'" :class="[classes, sizeClass]">
        <span v-if="$slots.icon" :class="iconClass">
            <slot name="icon"/>
        </span>
        <slot/>
    </div>

    <Link v-else :href="href" :class="[classes, sizeClass]" :as="linkAs" :method="linkMethod">
        <span v-if="$slots.icon" :class="iconClass">
            <slot name="icon"/>
        </span>
        <slot/>
    </Link>
</template>
