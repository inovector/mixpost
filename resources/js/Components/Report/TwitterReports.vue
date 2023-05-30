<script setup>
import Panel from "@/Components/Surface/Panel.vue";
import ChartBar from "@/Components/Package/ChartBar.vue";
import Alert from "../Util/Alert.vue";

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
        <div v-if="data.tier === 'free'" class="mb-lg">
            <Alert variant="warning" :closeable="false">
                <div>You are using Free Tier Twitter API. Reports may be limited.</div>
                <a href="https://developer.twitter.com/en/portal/dashboard" target="_blank" class="underline">Upgrade
                    Twitter API Tier</a>
            </Alert>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-sm">
            <Panel>
                <template #title><span v-tooltip="'The number of times where your posts were liked'">Likes</span>
                </template>
                <div class="font-bold text-indigo-500 text-2xl">{{ getMetricCount('likes') }}</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="'The number of times your tweets have been retweeted'">Retweets</span>
                </template>
                <div class="font-bold text-indigo-500 text-2xl">{{ getMetricCount('retweets') }}</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="'The number of times people saw your posts'">Impressions</span>
                </template>
                <div class="font-bold text-indigo-500 text-2xl">{{ getMetricCount('impressions') }}</div>
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
