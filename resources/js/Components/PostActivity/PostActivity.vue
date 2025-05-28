<script setup>
import NewComment from "./NewComment.vue";
import ItemCommentType from "./ItemCommentType.vue";
import TypeLayout from "./TypeLayout.vue";
import UnlockButton from "../Pro/UnlockButton.vue";
import Flex from "../Layout/Flex.vue";
</script>
<template>
    <div class="flex flex-col h-full relative">
        <div
            class="flex flex-col h-full overflow-y-scroll mixpost-scroll-style row-px row-pt pr-1 sm:pr-4! lg:pr-6! pb-md blur-xs">
            <ul class="space-y-md">
                <li>
                    <TypeLayout>
                        <template #default>
                            Aaron created this post
                        </template>
                        <template #created_at>
                            Dec 1, 10:25am
                        </template>
                    </TypeLayout>
                </li>
                <li>
                    <TypeLayout>
                        <template #default>
                            Aaron scheduled this post for Dec 1, 04:10pm and submitted it for approval
                        </template>
                        <template #created_at>
                            Dec 1, 10:50am
                        </template>
                    </TypeLayout>
                </li>
                <li>
                    <ItemCommentType name="Frank" created_at="Dec 1, 15:25">
                        <span class="text-indigo-500">@Aaron</span>, could you please enhance the engagement level of
                        this post? Thank you!
                    </ItemCommentType>
                </li>
                <li>
                    <TypeLayout>
                        <template #default>
                            Frank approved scheduling this post for Dec 1, 04:10pm
                        </template>
                        <template #created_at>
                            Dec 1, 04:01pm
                        </template>
                    </TypeLayout>
                </li>
            </ul>
        </div>

        <NewComment/>

        <div class="absolute w-full h-full">
            <Flex class="items-center h-full w-full row-px">
                <UnlockButton/>
            </Flex>
        </div>
    </div>
</template>
