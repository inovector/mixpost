<script setup>
import {nextTick, onMounted, onUnmounted, ref, watch} from "vue";
import Masonry from "masonry-layout";
import imagesLoaded from "imagesloaded";

const props = defineProps(['value']);

const grid = ref(null);
let masonry = null;

onMounted(() => {
    masonry = new Masonry(grid.value, {
        itemSelector: '.grid-item',
        columnWidth: '.grid-item',
        percentPosition: true,
    });
});

onUnmounted(() => {
    masonry.destroy();
});

const masonryDraw = function () {
    masonry.reloadItems()

    imagesLoaded(grid.value, () => {
        masonry.layout()
    })
}

nextTick(() => {
    masonryDraw();
})
</script>
<template>
    <div ref="grid">
        <slot/>
    </div>
</template>
