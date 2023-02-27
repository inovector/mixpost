<script setup>
import Panel from "@/Components/Surface/Panel.vue";
import ChartBar from "@/Components/Package/ChartBar.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true
    },
    isLoading: {
        type: Boolean,
        required: true,
    }
})

const getMetricCount = (value) => {
    return props.data.metrics.hasOwnProperty(value) ? props.data.metrics[value] : 0;
}

const getAudienceData = (value) => {
    return props.data.audience.hasOwnProperty(value) ? props.data.audience[value] : []
}
</script>
<template>
    <div class="row-px mt-2xl">
        <div class="grid grid-cols-3 gap-sm">
            <Panel>
                <template #title><span v-tooltip="'The number of times where your posts were liked'">Page Visits</span>
                </template>
                <div class="font-bold text-indigo-500 text-2xl">12</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="'The number of times your posts have been engaged'">Engagement</span>
                </template>
                <div class="font-bold text-indigo-500 text-2xl">14</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="'The number of times people saw your posts'">Impressions</span>
                </template>
                <div class="font-semibold text-indigo-500 text-2xl">40</div>
            </Panel>
        </div>
    </div>

    <div class="row-px mt-2xl">
        <Panel>
            <template #title>Audience</template>
            <template #description>The number of members per day during the selected period</template>
            <ChartBar :data="{
                labels: getAudienceData('labels'),
                datasets: [
                    {
                        label: 'Members',
                        type: 'line',
                        data: getAudienceData('values'),
                        borderColor: '#3F3795',
                        pointBackgroundColor: '#4F46BB',
                        pointBorderColor: '#4F46BB',
                        yAxisID: 'y1',
                        order: 0,
                    }
                ]
            }"/>
        </Panel>
    </div>
</template>
