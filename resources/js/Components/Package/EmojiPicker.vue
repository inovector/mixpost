<script setup>
import {defineAsyncComponent} from "vue";
import Dropdown from "@/Components/Dropdown/Dropdown.vue"
import EmojiIcon from "@/Icons/Emoji.vue"
import EmojiPreloader from "../Util/EmojiPreloader.vue";

const EmojiMart = defineAsyncComponent({
    loader: () => import("@/Components/Package/EmojiMart.vue"),
    loadingComponent: EmojiPreloader,
});

defineProps({
    closeOnSelect: {
        type: Boolean,
        default: false
    },
    tooltip: {
        type: String,
        default: 'Emoji'
    }
});

const emit = defineEmits(['selected', 'close'])

const select = (emoji) => {
    emit('selected', emoji);
}
</script>
<template>
    <Dropdown placement="bottom-start"
              :closeable-on-content="closeOnSelect"
              width-classes="w-auto"
              @close="$emit('close')">
        <template #trigger>
            <div class="flex">
                <button type="button" v-tooltip="tooltip"
                        class="hover:text-orange-500 transition-colors ease-in-out duration-200 outline-hidden text-stone-800">
                    <EmojiIcon/>
                </button>
            </div>
        </template>
        <template #content>
            <EmojiMart @select="select"/>
        </template>
    </Dropdown>
</template>
