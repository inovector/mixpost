<script setup>
import Flex from "../Layout/Flex.vue";
import CreatedAt from "./CreatedAt.vue";
import PureButton from "../Button/PureButton.vue";
import Emoji from "../../Icons/Emoji.vue";

defineProps({
    name: {
        type: String,
        required: true,
    },
    bg: {
        type: String,
        default: 'bg-lime-200',
    },
    created_at: {
        type: String,
        required: true,
    },
})
</script>
<template>
    <div class="group relative border bg-white border-gray-200 rounded-lg p-md">
        <Flex :responsive="false" class="justify-between">
            <Flex :responsive="false">
                <div :class="bg" class="w-7 h-7 rounded-full flex items-center justify-center p-xs">
                    <div class="text-sm">{{ name.charAt(0) }}</div>
                </div>
                <div>{{ name }}</div>
            </Flex>

            <CreatedAt>{{ created_at}}</CreatedAt>
        </Flex>

        <div class="mt-xs">
            <div>
                <slot/>
            </div>
        </div>

        <Flex :responsive="false" class="border-t border-gray-200 mt-xs pt-md items-start justify-between">
            <Flex :wrap="true">
                <div class="border border-gray-200 rounded-xl px-[6px] hover:bg-indigo transition-colors duration-200">
                    👍
                </div>

                <div class="border border-gray-200 rounded-xl px-[6px] hover:bg-indigo transition-colors duration-200">
                    ✅
                </div>
            </Flex>

            <PureButton>
                1 reply
            </PureButton>
        </Flex>
    </div>
</template>
