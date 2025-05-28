<script setup>
import {computed} from "vue";
import {Link} from '@inertiajs/vue3'
import ChevronRightIcon from "@/Icons/ChevronRight.vue"
import ChevronLeftIcon from "@/Icons/ChevronLeft.vue"

const props = defineProps({
    meta: {
        type: Object,
        default: {
            current_page: 1,
            from: 1,
            last_page: 1,
            per_page: 2,
            to: 1,
            total: 0,
            links: [],
        }
    },
    links: {
        type: Object,
        default: {
            first: null,
            last: null,
            next: null,
            prev: null,
        }
    }
})

const linkClass = 'px-sm py-xs rounded-md leading-4';

const formattedLinks = computed(() => {
    return props.meta.links.map((link) => {
        const label = link.label.replace('&laquo; ', '').replace(' &raquo;', '');

        return {
            active: link.active,
            url: link.url,
            label,
            component: isNaN(parseInt(link.label)) ? {
                'Next': ChevronRightIcon,
                'Previous': ChevronLeftIcon
            }[label] : null
        }
    });
})
</script>
<template>
    <div class="bg-white border border-gray-100 rounded-lg p-sm w-fit">
        <div class="flex flex-wrap items-center space-x-1">
            <template v-for="(link, index) in formattedLinks">
                <div v-if="link.url === null"
                     :key="index"
                     :class="[linkClass, {'px-0!': link.label === '...', 'px-xs!': link.component}]"
                     class="text-gray-400"
                >
                    <template v-if="link.component">
                        <component :is="link.component"/>
                    </template>
                    <template v-else>{{ link.label }}</template>
                </div>
                <Link v-else :key="`link-${index}`" disabled
                      class="transition-colors ease-in-out duration-200"
                      :class="[linkClass, { 'bg-indigo-500 text-white': link.active, 'hover:text-indigo-500 focus:text-indigo-500': !link.active, 'px-xs!': link.component }]"
                      :href="link.url">
                    <template v-if="link.component">
                        <component :is="link.component"/>
                    </template>
                    <template v-else>{{ link.label }}</template>
                </Link>
            </template>
        </div>
    </div>
</template>
