<script setup>
import {computed, ref} from "vue";
import usePostVersions from "@/Composables/usePostVersions";
import TableRow from "@/Components/DataDisplay/TableRow.vue";
import TableCell from "@/Components/DataDisplay/TableCell.vue";
import MediaFile from "@/Components/Media/MediaFile.vue";
import Tag from "@/Components/DataDisplay/Tag.vue";
import Dropdown from "@/Components/Dropdown/Dropdown.vue"
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue";
import DialogModal from "@/Components/Modal/DialogModal.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import PureButton from "@/Components/Button/PureButton.vue";
import PostPreviewProviders from "@/Components/Post/PostPreviewProviders.vue"
import Account from "@/Components/Account/Account.vue"
import PostItemActions from "@/Components/Post/PostItemActions.vue";
import PostStatus from "@/Components/Post/PostStatus.vue";
import VerticallyScrollableContent from "@/Components/Surface/VerticallyScrollableContent.vue";

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    filter: {
        type: Object,
        default: {
            accounts: [],
        }
    }
})

const {getOriginalVersion, getAccountVersion} = usePostVersions();

const content = computed(() => {
    if (!props.item.versions.length) {
        return {
            excerpt: '',
            media: null
        }
    }

    let accounts = props.item.accounts;

    if (props.filter.accounts.length) {
        accounts = accounts.filter(account => props.filter.accounts.includes(account.id))
    }

    const accountVersions = accounts.map((account) => {
        const accountVersion = getAccountVersion(props.item.versions, account.id);

        return accountVersion ? accountVersion.content[0] : getOriginalVersion(props.item.versions).content[0];
    })

    const record = accountVersions.length ? accountVersions[0] : props.item.versions[0].content[0];

    return {
        excerpt: record.excerpt,
        media: record.media.length ? record.media[0] : null,
    }
});

const preview = ref(false);

const openPreview = () => {
    preview.value = true;
}

const closePreview = () => {
    preview.value = false;
}
</script>
<template>
    <TableRow :hoverable="true">
        <TableCell class="w-10">
            <slot name="checkbox"/>
        </TableCell>
        <TableCell :clickable="true" @click="openPreview">
            <div class="w-44">
                <PostStatus :value="item.status"/>
                <div v-if="item.status === 'SCHEDULED'" class="text-sm mt-xs text-gray-500">{{
                        item.scheduled_at.human
                    }}
                </div>
                <div v-if="item.status === 'PUBLISHED'" class="text-sm mt-xs text-gray-500">{{
                        item.published_at.human
                    }}
                </div>
            </div>
        </TableCell>
        <TableCell :clickable="true" @click="openPreview" class="!pl-0">
            <div class="w-96 text-left">{{ content.excerpt }}</div>
        </TableCell>
        <TableCell :clickable="true" @click="openPreview">
            <div v-if="content.media" class="w-48 flex">
                <MediaFile v-if="content.media" :media="content.media" img-height="sm"/>
            </div>
        </TableCell>
        <TableCell :clickable="true" @click="openPreview">
            <div class="flex flex-wrap gap-xs">
                <Tag v-for="tag in item.tags" :key="tag.id" :item="tag" :removable="false" :editable="false"/>
            </div>
        </TableCell>
        <TableCell>
            <div class="flex gap-xs">
                <div v-for="(account, index) in item.accounts.slice(0, 3)" :key="account.id"
                     :class="{'-ml-6': index > 0}">
                    <Account :provider="account.provider"
                             :img-url="account.image"
                             :active="true"
                             v-tooltip="account.name"
                    />
                </div>
                <Dropdown v-if="item.accounts.length > 3" width-classes="w-64" placement="bottom-end">
                    <template #trigger>
                        <PureButton class="mt-4 font-semibold">+{{ item.accounts.slice(3).length }}</PureButton>
                    </template>

                    <template #content>
                        <VerticallyScrollableContent>
                            <template v-for="account in item.accounts.slice(3)">
                                <DropdownItem as="div">
                                    <span class="mr-xs">
                                        <Account :provider="account.provider"
                                                 :img-url="account.image"
                                                 :active="true"/>
                                    </span>
                                    <span class="text-left">{{ account.name }}</span>
                                </DropdownItem>
                            </template>
                        </VerticallyScrollableContent>
                    </template>
                </Dropdown>
            </div>
        </TableCell>
        <TableCell>
            <PostItemActions :item-id="item.id"/>
        </TableCell>

        <DialogModal :show="preview" :scrollableBody="true" @close="closePreview">
            <template #body>
                <PostPreviewProviders v-if="preview"
                                      :accounts="item.accounts"
                                      :selected-accounts="item.accounts.map(account => account.id)"
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
    </TableRow>
</template>
