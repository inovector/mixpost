<script setup>
import {computed, inject} from "vue";
import {get} from "lodash"
import MixpostPostPreviewTwitter from "@/Components/PostPreviewTwitter.vue"
import MixpostPanel from "@/Components/Panel.vue";

const postContext = inject('postContext')

const props = defineProps({
    accounts: {
        required: true,
        type: Array,
    },
    selectedAccounts: {
        required: true,
        type: Array,
    },
    body: {
        required: true,
    }
});

const previews = computed(() => {
    return props.accounts.filter((account) => {
        return props.selectedAccounts.includes(account.id);
    }).map((account) => {
        return {
            account,
            providerComponent: {
                'twitter': MixpostPostPreviewTwitter,
            }[account.provider]
        }
    });
})
</script>
<template>
    <template v-if="selectedAccounts.length">
        <template v-for="preview in previews" :key="preview.id">
            <div class="mb-6 last:mb-0">
                <component :is="preview.providerComponent"
                           :name="preview.account.name"
                           :username="preview.account.username"
                           :image="preview.account.image"
                           :body="body"
                           :reached-max-character-limit="get(postContext.reachedMaxCharacterLimit, preview.account.provider, false)"
                />
            </div>
        </template>
    </template>
    <template v-else>
        <div>
            <div>Hi 👋</div>
            <div>Select an account and start writing your post in the left panel to start.</div>
        </div>
        <MixpostPanel class="mt-6">
            <div class="flex items-start">
                <div class="mr-3">
                    <span
                        class="inline-flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full bg-gray-100"></span>
                </div>
                <div class="w-full">
                    <div class="flex items-center">
                        <div class="mr-2 bg-gray-100 w-32 h-4"></div>
                        <div class="bg-gray-100 w-24 h-4"></div>
                    </div>
                    <div class="mt-5">
                        <div class="bg-gray-100 w-3/4 h-4"></div>
                        <div class="bg-gray-100 w-full h-4 mt-2"></div>
                        <div class="bg-gray-100 w-2/5 h-4 mt-2"></div>
                    </div>
                </div>
            </div>
        </MixpostPanel>
    </template>
</template>
