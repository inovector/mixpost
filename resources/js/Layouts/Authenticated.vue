<script setup>
import Sidebar from "@/Components/Sidebar/Sidebar.vue";
import Navigation from "@/Components/Navigation/NavBar.vue";
import Notifications from "@/Components/Util/Notifications.vue";

import {onUnmounted, provide, reactive} from "vue";
import {router} from "@inertiajs/vue3";

const context = reactive({
    showAside: false,
    dashboard_filter: {
        account_id: null,
        period: '30_days'
    }
});

provide('appContext', context);

const removeStartEventListener = router.on('start', () => {
    context.showAside = false;
});

onUnmounted(() => {
    removeStartEventListener();
})
</script>
<template>
    <div class="flex flex-row h-screen min-h-full bg-stone-500">
        <aside :class="{'translate-x-0': context.showAside, '-translate-x-full xl:translate-x-0': !context.showAside}"
               class="aside fixed xl:relative h-full z-40 transition-transform ease-in-out duration-200">
            <Sidebar/>
        </aside>

        <main class="w-full xl:main flex flex-col overflow-y-auto" scroll-region>
            <Navigation/>
            <slot/>
        </main>

        <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-show="context.showAside" @click="context.showAside = false"
                 class="fixed inset-0 z-10 transform transition-all">
                <div class="absolute inset-0 bg-indigo-900 opacity-60"/>
            </div>
        </transition>

        <Notifications/>
    </div>
</template>

