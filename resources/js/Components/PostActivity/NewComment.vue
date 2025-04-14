<script setup>
import Flex from "../Layout/Flex.vue";
import PrimaryButton from "../Button/PrimaryButton.vue";
import Emoji from "../../Icons/Emoji.vue";
import Sparkles from "../../Icons/Sparkles.vue";
</script>
<template>
    <div class="row-px blur-xs">
        <div
            class="border bg-white border-gray-200 rounded-lg p-md pb-xs text-base transition-colors ease-in-out duration-200">
            <div class="editable" contenteditable="true" data-placeholder="Write a comment..." aria-disabled="true"/>

            <Flex :responsive="false"
                  class="relative justify-between border-t border-gray-200 pt-xs mt-md items-center">
                <Flex>
                    <Emoji/>
                    <Sparkles/>
                </Flex>
                <PrimaryButton>
                    Send
                </PrimaryButton>
            </Flex>
        </div>
    </div>
</template>
<style lang="css" scoped>
.editable[data-placeholder]:empty:before {
    content: attr(data-placeholder);
    color: #999;
}
.editable.empty:before {
    content: attr(data-placeholder);
    color: #999;
}
</style>
