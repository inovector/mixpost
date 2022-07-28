<script setup>
import data from "emoji-mart-vue-fast/data/apple.json";
import "emoji-mart-vue-fast/css/emoji-mart.css";
import {Picker, EmojiIndex} from "emoji-mart-vue-fast/src";
import emojiRegex from 'emoji-regex'
import EmojiIcon from "@/Icons/Emoji.vue"

const emojiIndex = new EmojiIndex(data);
const unicodeEmojiRegex = emojiRegex()

const emit = defineEmits(['selected'])

function emojiToHtml(emoji) {
    let style = `background-position: ${emoji.getPosition()}; width: 24px; height: 24px;`
    return `<span class="emoji-mart-emoji"><span class='emoji-set-apple emoji-type-image' style="${style}"></span></span>`
}

function showEmoji(emoji) {
    emit('selected', emoji);
}

function wrapEmoji(text) {
    return text.replace(unicodeEmojiRegex, function (match, offset) {
        const before = text.substring(0, offset)
        if (endsWith(before, 'alt="') || endsWith(before, 'data-text="')) {
            // Emoji inside the replaced <img>
            return match
        }
        // Find emoji object by native emoji.
        let emoji = emojiIndex.nativeEmoji(match)
        if (!emoji) {
            // Can't find unicode emoji in our index
            return match
        }
        // See `emojiToHtml` function above.
        return emojiToHtml(emoji)
    })
}
</script>
<template>
    <div>
        <button type="button" v-tooltip="'Emoji'" class="hover:text-orange-500 transition-colors ease-in-out duration-200 outline-none text-stone-800">
            <EmojiIcon/>
        </button>
<!--        <Picker :data="emojiIndex"-->
<!--                :auto-focus="true"-->
<!--                :show-preview="false"-->
<!--                set="apple"-->
<!--                @select="showEmoji"-->
<!--        />-->
    </div>
</template>
<style>
.emoji-type-image.emoji-set-apple {
    background-image: url('https://unpkg.com/emoji-datasource-apple@14.0.0/img/apple/sheets-256/32.png') !important;
}
</style>
