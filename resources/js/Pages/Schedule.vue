<script setup>
import {Head} from '@inertiajs/inertia-vue3';
import useSettings from "@/Composables/useSettings";
import CalendarMonth from "@/Components/Schedule/Month/CalendarMonth.vue";
import Panel from "@/Components/Surface/Panel.vue";

const {timeZone, weekStartsOn} = useSettings();
</script>
<template>
    <Head title="Schedule"/>

    <div class="row-py mb-2xl">
        <div class="w-full row-px">
            <Panel :withPadding="false">
                <CalendarMonth :weekStartsOn="weekStartsOn" :timeZone="timeZone"/>
            </Panel>
        </div>
    </div>
</template>
