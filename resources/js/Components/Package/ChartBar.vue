<script setup>
import {onBeforeUnmount, onMounted, ref, toRaw, watch} from "vue";
import {toRawIfProxy} from "@/helpers";
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

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
})

const dom = ref();
const chartRef = ref(null);
const zoomed = ref(false);

const resetZoom = () => {
    if (!chartRef.value) {
        return;
    }

    chartRef.value.resetZoom();
    zoomed.value = false;
}

onMounted(() => {
    chartRef.value = new Chart(dom.value.getContext('2d'), {
        type: 'bar',
        data: {
            labels: props.data.labels,
            datasets: props.data.datasets
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
                        }
                    },
                },
            },
            scales: {
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

onBeforeUnmount(() => {
    const chart = toRaw(chartRef.value)

    if (chart) {
        chart.destroy();
        chartRef.value = null
    }
})

watch(() => props.data, (newValue, oldValue) => {
    const chart = toRaw(chartRef.value)

    if (!chart) {
        return
    }

    let shouldUpdate = false

    if (newValue) {
        const newLabels = toRawIfProxy(newValue.labels)
        const oldLabels = toRawIfProxy(oldValue.labels)
        const newDatasets = toRawIfProxy(newValue.datasets)
        const oldDatasets = toRawIfProxy(oldValue.datasets)

        if (newLabels !== oldLabels) {
            chart.config.data.labels = newLabels;
            shouldUpdate = true
        }

        if (newDatasets && newDatasets !== oldDatasets) {
            chart.config.data.datasets = newDatasets;
            shouldUpdate = true
        }
    }

    if (shouldUpdate) {
        chart.update('active');
    }
}, {
    deep: true
});
</script>
<template>
    <div>
        <div class="relative">
            <canvas ref="dom" style="position: relative; max-height:300px; width:100%; max-width: 100%;"></canvas>
        </div>
        <div class="text-right mt-4">
            <small class="text-gray-500 text-sm">You can drag the chart to zoom.</small>
            <button v-if="zoomed" @click="resetZoom"
                    class="ml-xs text-sm hover:text-indigo-500 transition-colors ease-in-out duration-200">Reset zoom
            </button>
        </div>
    </div>
</template>
