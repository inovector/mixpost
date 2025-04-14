<script setup>
import {computed, inject, ref} from "vue";
import {uniqBy} from "lodash";
import {convertTime24to12} from "@/helpers";
import useSettings from "@/Composables/useSettings";
import usePostVersions from "@/Composables/usePostVersions";
import ProviderIcon from "@/Components/Account/ProviderIcon.vue";
import PostStatus from "@/Components/Post/PostStatus.vue";
import DialogModal from "@/Components/Modal/DialogModal.vue";
import PostItemActions from "@/Components/Post/PostItemActions.vue";
import PostPreviewProviders from "@/Components/Post/PostPreviewProviders.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import ClockIcon from "@/Icons/Clock.vue";

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

const calendarFilter = inject('calendarFilter');

const {timeFormat} = useSettings();
const {getOriginalVersion, getAccountVersion} = usePostVersions();

const content = computed(() => {
    if (!props.item.versions.length) {
        return {
            excerpt: '',
        }
    }

    let accounts = props.item.accounts;

    if (calendarFilter.value.accounts.length) {
        accounts = accounts.filter(account => calendarFilter.value.accounts.includes(account.id))
    }

    const accountVersions = accounts.map((account) => {
        const accountVersion = getAccountVersion(props.item.versions, account.id);

        return accountVersion ? accountVersion.content[0] : getOriginalVersion(props.item.versions).content[0];
    })

    const record = accountVersions.length ? accountVersions[0] : props.item.versions[0].content[0];

    return {
        excerpt: record.excerpt
    }
});

const accounts = computed(() => {
    return uniqBy(props.item.accounts, 'provider');
})

const time = computed(() => {
    if (timeFormat === 12) {
        return convertTime24to12(props.item.scheduled_at.time);
    }

    return props.item.scheduled_at.time;
})

const preview = ref(false);

const openPreview = () => {
    preview.value = true;
}

const closePreview = () => {
    preview.value = false;
}
</script>
<template>
    <div
        class="w-full relative flex rounded-md overflow-hidden border border-gray-200 hover:border-indigo-500 transition-colors ease-in-out duration-200"
        @click="openPreview"
        role="button"
        aria-pressed="false"
        tabindex="0">
        <div v-if="item.tags.length" class="flex flex-col h-full">
            <div v-for="tag in item.tags" class="w-sm h-full first:rounded-tl-md last:rounded-bl-md"
                 :style="{backgroundColor: tag.hex_color}"/>
        </div>

        <div class="w-full h-full p-1 md:p-sm bg-white">
            <div class="text-left text-sm md:text-base">{{ content.excerpt }}</div>

            <div v-if="accounts.length" class="flex flex-wrap gap-xs items-center mt-xs">
                <div v-for="account in accounts" :key="account.id">
                    <ProviderIcon v-tooltip="`${account.name}`" :provider="account.provider" class="w-4! h-4!"/>
                </div>
            </div>

            <div class="flex items-center justify-between mt-xs">
                <div class="flex items-center text-gray-500">
                    <ClockIcon class="hidden md:block mr-1 w-5! h-5!"/>
                    <span class="text-sm">{{ time }}</span>
                </div>
                <PostStatus :value="item.status" :showName="false" class="hidden md:block"/>
            </div>
        </div>
    </div>

    <DialogModal :show="preview" :scrollableBody="true" @close="closePreview">
        <template #body>
            <PostStatus :value="item.status" class="mb-lg"/>

            <PostPreviewProviders v-if="preview"
                                  :accounts="item.accounts"
                                  :versions="item.versions"
            />
        </template>
        <template #footer>
            <template v-if="preview">
                <div class="mr-xs">
                    <PostItemActions :item-id="item.id"/>
                </div>
                <SecondaryButton @click="closePreview">Close</SecondaryButton>
            </template>
        </template>
    </DialogModal>
</template>
