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
