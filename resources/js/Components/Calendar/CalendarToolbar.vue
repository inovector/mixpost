<script setup>
import {inject} from "vue";
import PostsFilter from '@/Components/Post/PostsFilter.vue';
import CalendarSwitchType from "@/Components/Calendar/CalendarSwitchType.vue";

const calendarFilter = inject('calendarFilter');
</script>
<template>
    <div class="flex items-center space-x-md">
        <PostsFilter v-model="calendarFilter"/>
        <CalendarSwitchType/>
    </div>
</template>
