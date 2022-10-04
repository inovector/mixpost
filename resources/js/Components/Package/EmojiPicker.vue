<script setup>
import data from "emoji-mart-vue-fast/data/apple.json";
import "emoji-mart-vue-fast/css/emoji-mart.css";
import {Picker, EmojiIndex} from "emoji-mart-vue-fast/src";
import Dropdown from "@/Components/Dropdown/Dropdown.vue"
import EmojiIcon from "@/Icons/Emoji.vue"

const emojiIndex = new EmojiIndex(data);

const emit = defineEmits(['selected', 'close'])

function showEmoji(emoji) {
    emit('selected', emoji);
}
</script>
<template>
    <Dropdown placement="bottom-start" :closeable-on-content="false" width-classes="w-auto" @close="$emit('close')">
        <template #trigger>
            <button type="button" v-tooltip="'Emoji'"
                    class="hover:text-orange-500 transition-colors ease-in-out duration-200 outline-none text-stone-800">
                <EmojiIcon/>
            </button>
        </template>
        <template #content>
            <Picker :data="emojiIndex"
                    :auto-focus="true"
                    :show-preview="false"
                    :native="true"
                    @select="showEmoji"
            />
        </template>
    </Dropdown>
</template>
