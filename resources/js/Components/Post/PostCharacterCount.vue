<script setup>
import usePostCharacterLimit from "../../Composables/usePostCharacterLimit";
import {onBeforeUnmount, onMounted, watch} from "vue";
import {debounce} from "lodash";
import emitter from "@/Services/emitter";

const props = defineProps({
    selectedAccounts: {
        type: Array,
        required: true,
    },
    versions: {
        type: Array,
        required: true,
    },
    activeVersion: {
        type: Number,
        required: true,
    },
    activeContent: {
        type: Number,
        required: true,
    }
});

const emits = defineEmits(['updateMaxCharLimit']);

const {currentCharLeft, currentCharMaxLimit, getCharMaxLimit} = usePostCharacterLimit(props);

const handleUpdate = () => {
    emits('updateMaxCharLimit', getCharMaxLimit(props.activeVersion)?.limit || 0);
};

watch(currentCharLeft, debounce(() => {
    if (!currentCharMaxLimit.value) return;

    emits('updateMaxCharLimit', currentCharMaxLimit.value.limit || 0);
}, 100));

watch(() => props.versions.length, handleUpdate);
watch(() => props.selectedAccounts, handleUpdate);

onMounted(() => {
    emitter.on('postVersionContentDeleted', () => {
        handleUpdate();
    });

    handleUpdate();
});

onBeforeUnmount(() => {
    emitter.off('postVersionContentDeleted');
});
</script>
<template>
    <div v-if="currentCharLeft !== null" class="flex justify-center">
        <div
            :class="{'text-stone-800': currentCharLeft >= 0, 'text-orange-500': currentCharLeft * 100 / currentCharMaxLimit.limit < 20, 'text-red-500': currentCharLeft < 0}">
            {{ currentCharLeft }}
        </div>
    </div>
</template>
