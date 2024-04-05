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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-sm">
            <!--            Facebook deprecated `page_engaged_users` metric-->
<!--            <Panel>-->
<!--                <template #title><span v-tooltip="'The number of people who engaged with your Page. Engagement includes any click.'">Page Engaged Users</span>-->
<!--                </template>-->
<!--                <div class="font-bold text-indigo-500 text-2xl">{{ getMetricCount('page_engaged_users') }}</div>-->
<!--            </Panel>-->

            <Panel>
                <template #title><span v-tooltip="'The number of times people have engaged with your posts through reactions, comments, shares and more.'">Post Engagements</span>
                </template>
                <div class="font-bold text-indigo-500 text-2xl">{{ getMetricCount('page_post_engagements') }}</div>
            </Panel>

            <Panel>
                <template #title><span v-tooltip="'The number of times your Page\'s posts entered a person\'s screen. Posts include statuses, photos, links, videos and more.'">Posts Impressions</span>
                </template>
                <div class="font-semibold text-indigo-500 text-2xl">{{ getMetricCount('page_posts_impressions') }}</div>
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
