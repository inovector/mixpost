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
    },
    external: {
        type: Boolean,
        default: false
    },
    externalTarget: {
        type: String,
        default: '_self'
    }
});
</script>
<template>
    <template v-if="!external">
        <Link :href="url"
              class="py-xs flex flex-row items-center space-x-md rtl:space-x-reverse transition ease-in-out duration-200"
              :class="{'text-gray-900': active, 'text-gray-400 hover:text-gray-700': !active}">
            <div>
                <slot name="icon"/>
            </div>
            <div class="font-medium tracking-tight rtl:tracking-normal">
                <slot/>
            </div>
        </Link>
    </template>

    <template v-else>
        <a :href="url" :target="externalTarget"
           class="py-xs flex flex-row items-center space-x-4 transition ease-in-out duration-200"
           :class="{'text-gray-900': active, 'text-gray-400 hover:text-gray-700': !active}">
            <div>
                <slot name="icon"/>
            </div>
            <div class="font-medium tracking-tight rtl:tracking-normal">
                <slot/>
            </div>
        </a>
    </template>
</template>
