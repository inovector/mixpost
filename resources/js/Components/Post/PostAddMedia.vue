<script setup>
import AddMedia from "@/Components/Media/AddMedia.vue";
import PhotoIcon from "@/Icons/Photo.vue";

defineEmits(["insert"]);
</script>

<template>
    <AddMedia @insert="$emit('insert', $event)">
        <button
            type="button"
            v-tooltip="`Media`"
            class="text-stone-800 hover:text-indigo-500 transition-colors ease-in-out duration-200"
        >
            <PhotoIcon/>
        </button>
    </AddMedia>
</template>
