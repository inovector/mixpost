<script setup>
import {nextTick, onMounted, onUnmounted, ref, watch} from "vue";
import {maxBy, debounce} from "lodash";
import {getWindowDimensions} from "@/helpers";

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    columns: {
        type: Number,
        default: 3,
    }
})

const bottomRef = ref(null);
const columns = ref([]);
const cursor = ref(0);
const ready = ref(false);

onMounted(() => {
    redraw();
    window.addEventListener("resize", resizeHandler);
});

onUnmounted(() => {
    window.removeEventListener("resize", resizeHandler);
})

const resizeHandler = debounce(() => {
    if (columns.value.length !== newColumns().length) {
        redraw();
    }
}, 300);

const newColumns = () => {
    let count = props.columns;

    if (getWindowDimensions().width <= 768) {
        count = 2;
    }

    const columns = []

    for (let i = 0; i < count; i++) {
        columns.push({i: i, indexes: []})
    }

    return columns
}

const addItem = (index) => {
    const column = columns.value[index]

    if (props.items[cursor.value]) {
        column.indexes.push(cursor.value)
        cursor.value++
    }
}

const fill = () => {
    if (!ready.value) {
        return
    }

    if (cursor.value >= props.items.length) {
        return
    }

    // Keep filling until no more items
    nextTick(() => {
        const bottom = maxBy(bottomRef.value, (spacer) => spacer.clientHeight || 0)

        setTimeout(()=> {
            addItem(bottom.dataset.column)
            fill()
        });
    })
}

const redraw = () => {
    ready.value = false;
    columns.value.splice(0);
    cursor.value = 0;
    columns.value.push(...newColumns())
    ready.value = true;
    fill();
}

watch(() => props.items, () => {
    fill();
});
</script>
<template>
    <div class="flex -m-1" :class="{'opacity-0': !ready}">
        <div class="flex flex-col grow basis-0 px-1" v-for="(column, index) in columns" :key="index">

            <div class="py-1" v-for="i in column.indexes" :key="i" :ref="`item_${i}`">
                <slot v-bind:item="items[i]" :index="i">{{ items[i] }}</slot>
            </div>

            <div class="grow -mt-20 pt-20 min-h-[100px] -z-10" ref="bottomRef" :data-column="index"/>
        </div>
    </div>
</template>
