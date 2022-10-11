<script setup>
import { Head } from '@inertiajs/inertia-vue3';
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Panel from "@/Components/Surface/Panel.vue";
</script>
<template>
    <Head title="Dashboard" />

   <div class="default-x-padding default-y-padding">
       <div class="flex mx-4 justify-between items-center">
           <div class="flex flex-col items-start">
               <div class="text-4xl font-semibold text-center mb-2">
                   <span class="daytime-emoji emoji mr-3">☀️</span>
                   <span class="daytime-text">Good morning, Genius!</span>
               </div>
               <div class="text-base text-center mt-3">
                   Here's what's happening with your socials today.
               </div>
           </div>
           <PrimaryButton>Create</PrimaryButton>
       </div>

       <div class="default-t-margin">
           <Panel>
                Content
           </Panel>
       </div>
   </div>
</template>
