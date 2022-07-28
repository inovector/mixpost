<script setup>
import Sidebar from "@/Components/Sidebar.vue";
</script>
<template>
    <div class="flex flex-row h-screen min-h-full bg-stone-500">
        <div class="flex w-[290px] h-full">
            <sidebar/>
        </div>
        <div class="w-full ">
            <slot />
        </div>
    </div>
</template>

