<script setup>
import Panel from "@/Components/Surface/Panel.vue";
import ChartBar from "@/Components/Package/ChartBar.vue";
</script>
<template>
    <div class="row-px mt-2xl">
        <div class="grid grid-cols-3 gap-sm">
            <Panel>
                <template #title><span v-tooltip="'The number of times where your posts were liked'">Likes</span></template>
                <div class="font-bold text-indigo-500 text-2xl">12</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="'The number of times your tweets have been retweeted'">Retweets</span></template>
                <div class="font-bold text-indigo-500 text-2xl">14</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="'The number of times people saw your posts'">Impressions</span></template>
                <div class="font-bold text-indigo-500 text-2xl">40</div>
            </Panel>
        </div>
    </div>

    <div class="row-px mt-2xl">
        <Panel>
            <template #title>Audience growth</template>
            <template #description>Number of followers gained during the selected period</template>
            <ChartBar/>
        </Panel>
    </div>
</template>
