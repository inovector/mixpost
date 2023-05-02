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
                <template #title><span v-tooltip="'The number of replies to your posts'">Replies</span>
                </template>
                <div class="font-bold text-indigo-500 text-2xl">{{ getMetricCount('replies') }}</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="'The number of times your posts have been reblogs'">Reblogs</span>
                </template>
                <div class="font-bold text-indigo-500 text-2xl">{{ getMetricCount('reblogs') }}</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="'The number of times your posts have been added to favorites'">Favourites</span>
                </template>
                <div class="font-bold text-indigo-500 text-2xl">{{ getMetricCount('favourites') }}</div>
            </Panel>
        </div>
    </div>

    <div class="row-px mt-2xl">
        <Panel>
            <template #title>Audience</template>
            <template #description>The number of followers per day during the selected period</template>
            <ChartBar :data="{
                labels: getAudienceData('labels'),
                datasets: [
                    {
                        label: 'Followers',
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
