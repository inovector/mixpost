<script setup>
import Logo from "@/Components/DataDisplay/Logo.vue"
import Notifications from "@/Components/Util/Notifications.vue";
</script>
<template>
    <div class="flex flex-col h-screen min-h-full row-py row-px overflow-y-auto bg-stone-500" scroll-region>
        <div class="w-full max-w-(--container-5xl) mx-auto">
            <div class="relative mb-12 px-xl">
                <Logo class="h-14 mx-auto"/>
            </div>

            <slot/>
        </div>

        <Notifications/>
    </div>
</template>
