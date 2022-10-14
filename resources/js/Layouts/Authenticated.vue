<script setup>
import Sidebar from "@/Components/Sidebar/Sidebar.vue";
import Navigation from "@/Components/Navigation/NavBar.vue";
import Notifications from "@/Components/Util/Notifications.vue";

import {provide, reactive} from "vue";

const context = reactive({
    showAside: false,
});

provide('appContext', context);
</script>
<template>
    <div class="flex flex-row h-screen min-h-full bg-stone-500">
        <aside :class="{'translate-x-0': context.showAside, '-translate-x-full md:translate-x-0': !context.showAside}" class="aside fixed md:relative h-full z-50 transition-transform ease-in-out duration-200">
            <Sidebar/>
        </aside>

        <main class="w-full md:main flex flex-col overflow-y-auto" scroll-region>
            <Navigation/>
            <slot />
        </main>

        <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-show="context.showAside" @click="context.showAside = false" class="fixed inset-0 z-10 transform transition-all">
                <div class="absolute inset-0 bg-indigo-900 opacity-60" />
            </div>
        </transition>

        <Notifications/>
    </div>
</template>

