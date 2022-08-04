<script setup>
import Sidebar from "@/Components/Sidebar.vue";
import Notifications from "@/Components/Notifications.vue";
</script>
<template>
    <div class="flex flex-row h-screen min-h-full bg-stone-500">
        <div class="flex w-[290px] h-full">
            <sidebar/>
        </div>
        <div class="w-full ">
            <slot />
        </div>
        <Notifications/>
    </div>
</template>

