<script setup>
import {onMounted, onUnmounted, ref} from "vue";
import {
    Chart,
    BarController,
    LineController,
    LineElement,
    PointElement,
    BarElement,
    CategoryScale,
    LinearScale,
    Tooltip,
} from "chart.js";

import zoomPlugin from 'chartjs-plugin-zoom';

Chart.register(BarController, LineController, PointElement, LineElement, BarElement, CategoryScale, LinearScale, Tooltip, zoomPlugin);

// const props = defineProps({
//
// })

const dom = ref();
const chart = ref();
const zoomed = ref(false);

const labels = JSON.parse('[\u0022Nov 11\u0022,\u0022Nov 12\u0022,\u0022Nov 13\u0022,\u0022Nov 14\u0022,\u0022Nov 15\u0022,\u0022Nov 16\u0022,\u0022Nov 17\u0022,\u0022Nov 18\u0022,\u0022Nov 19\u0022,\u0022Nov 20\u0022,\u0022Nov 21\u0022,\u0022Nov 22\u0022,\u0022Nov 23\u0022,\u0022Nov 24\u0022,\u0022Nov 25\u0022,\u0022Nov 26\u0022,\u0022Nov 27\u0022,\u0022Nov 28\u0022,\u0022Nov 29\u0022,\u0022Nov 30\u0022,\u0022Dec 01\u0022,\u0022Dec 02\u0022,\u0022Dec 03\u0022,\u0022Dec 04\u0022,\u0022Dec 05\u0022,\u0022Dec 06\u0022,\u0022Dec 07\u0022,\u0022Dec 08\u0022,\u0022Dec 09\u0022,\u0022Dec 10\u0022,\u0022Dec 11\u0022,\u0022Dec 12\u0022,\u0022Dec 13\u0022,\u0022Dec 14\u0022,\u0022Dec 15\u0022,\u0022Dec 16\u0022,\u0022Dec 17\u0022,\u0022Dec 18\u0022,\u0022Dec 19\u0022,\u0022Dec 20\u0022,\u0022Dec 21\u0022,\u0022Dec 22\u0022,\u0022Dec 23\u0022,\u0022Dec 24\u0022,\u0022Dec 25\u0022,\u0022Dec 26\u0022,\u0022Dec 27\u0022,\u0022Dec 28\u0022,\u0022Dec 29\u0022,\u0022Dec 30\u0022,\u0022Dec 31\u0022,\u0022Jan 01\u0022,\u0022Jan 02\u0022,\u0022Jan 03\u0022,\u0022Jan 04\u0022,\u0022Jan 05\u0022,\u0022Jan 06\u0022,\u0022Jan 07\u0022,\u0022Jan 08\u0022,\u0022Jan 09\u0022,\u0022Jan 10\u0022,\u0022Jan 11\u0022]');
const subscribers = JSON.parse('[162,167,169,172,175,176,176,177,180,184,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194,194]')
const subscribes = JSON.parse('[3,5,2,3,3,1,0,1,3,4,10,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]');

onMounted(() => {
    chart.value = new Chart(dom.value.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Follows',
                    backgroundColor: '#DCDAF1',
                    hoverBackgroundColor: '#B8B4E4',
                    borderRadius: 5,
                    data: subscribes,
                    stack: 'stack0',
                    order: 2,
                },
                {
                    label: 'Followers',
                    type: 'line',
                    data: subscribers,
                    borderColor: '#3F3795',
                    pointBackgroundColor: '#4F46BB',
                    pointBorderColor: '#4F46BB',
                    yAxisID: 'y1',
                    order: 0,
                },
            ]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            barPercentage: 0.75,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                zoom: {
                    pan: {
                        enabled: true,
                        mode: 'x',
                        modifierKey: 'ctrl',
                    },
                    zoom: {
                        drag: {
                            enabled: true,
                        },
                        mode: 'x',
                        onZoomComplete: () => (zoomed.value = true),
                    },
                },
                tooltip: {
                    backgroundColor: '#1F1B4B',
                    titleSpacing: 4,
                    bodySpacing: 8,
                    padding: 20,
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            let label = context.dataset.label || '';
                            let value = context.raw;

                            if (typeof value === 'number') {
                                value = Math.abs(value);
                            }

                            return `${label}: ${value}`;
                        },
                        afterBody: tooltips => {
                            // const campaigns = this.chartData.campaigns[tooltips[0].dataIndex];
                            //
                            // if (campaigns.length === undefined || campaigns.length === 0) {
                            //     return;
                            // }
                            //
                            // return `Campaign${campaigns.length > 1 ? 's' : ''}: ${campaigns
                            //     .map(campaign => campaign.name)
                            //     .join(', ')}`;
                        },
                    },
                },
            },
            scales: {
                y: {
                    display: false,
                },
                y1: {
                    ticks: {
                        color: 'rgba(100, 116, 139, 1)',
                        precision: 0,
                    },
                    position: 'left',
                    beginAtZero: false,
                    grid: {
                        display: false,
                    },
                },
                x: {
                    ticks: {
                        autoSkip: true,
                        maxRotation: 0,
                        color: 'rgba(100, 116, 139, 1)',
                    },
                    grid: {
                        borderColor: 'rgba(100, 116, 139, .2)',
                        borderDash: [5, 5],
                        zeroLineColor: 'rgba(100, 116, 139, .2)',
                        zeroLineBorderDash: [5, 5],
                    },
                },
            },
        },
    })
})

onUnmounted(() => {
    if (chart.value) {
        chart.value.destroy();
    }
})

const resetZoom = () => {
    if (!chart.value) {
        return;
    }

    chart.value.resetZoom();
    zoomed.value = false;
}
</script>
<template>
    <canvas ref="dom" style="position: relative; max-height:300px; width:100%; max-width: 100%;"></canvas>
    <div class="text-right mt-4">
        <small class="text-gray-500 text-sm">You can drag the chart to zoom.</small>
        <button v-if="zoomed" @click="resetZoom" class="ml-xs text-sm hover:text-indigo-500 transition-colors ease-in-out duration-200">Reset zoom</button>
    </div>
</template>
