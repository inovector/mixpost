<script setup>
import EditorButton from "../Button/EditorButton.vue";
import ProLabel from "./ProLabel.vue";
import UpgradePro from "./UpgradePro.vue";

defineProps({
    tooltip: {
        type: String,
        required: true
    },
    showLabelHover: {
        type: Boolean,
        default: true
    }
});
</script>
<template>
    <div class="relative group z-10">
        <UpgradePro>
            <template #trigger>
                <EditorButton v-tooltip="tooltip" class="text-indigo-200!">
                    <slot/>
                </EditorButton>
            </template>
        </UpgradePro>

        <div :class="{'opacity-0 group-hover:opacity-100 transition-opacity ease-in-out duration-200': showLabelHover}" class="absolute top-0 right-0 -mt-xs -mr-xs">
            <ProLabel :withTitle="false" paddingClass="py-[1px] px-[1px]"/>
        </div>
    </div>
</template>
