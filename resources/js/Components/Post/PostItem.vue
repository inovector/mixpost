<script setup>
import {computed, ref} from "vue";
import Checkbox from "@/Components/Form/Checkbox.vue";
import TableRow from "@/Components/DataDisplay/TableRow.vue";
import TableCell from "@/Components/DataDisplay/TableCell.vue";
import MediaFile from "@/Components/Media/MediaFile.vue";
import Tag from "@/Components/DataDisplay/Tag.vue";
import DialogModal from "@/Components/Modal/DialogModal.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import PostPreviewProviders from "@/Components/Post/PostPreviewProviders.vue"
import Account from "@/Components/Account/Account.vue"
import PostItemActions from "@/Components/Post/PostItemActions.vue";
import PostStatus from "@/Components/Post/PostStatus.vue";

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    accounts: {
        type: Array,
        default: []
    }
})

const content = computed(() => {
    if (!props.item.versions.length) {
        return {
            excerpt: '',
            media: null
        }
    }

    if (!props.accounts.length) {
        const first = props.item.versions[0].content[0];

        return {
            excerpt: first.excerpt,
            media: first.media.length ? first.media[0] : null,
        }
    }
});

const preview = ref(false);

const onDelete = () => {
    preview.value = false;
}
</script>
<template>
    <TableRow :hoverable="true">
        <TableCell class="w-10">
            <Checkbox :value="item.id"/>
        </TableCell>
        <TableCell :clickable="true" @click="preview = true" class="w-48">
            <div @click="preview = true" tabindex="0" role="button">
                <PostStatus :value="item.status"/>
                <div v-if="item.status === 'SCHEDULED'" class="text-sm mt-2 text-gray-500">{{ item.scheduled_at.human }}</div>
                <div v-if="item.status === 'PUBLISHED'" class="text-sm mt-2 text-gray-500">{{ item.delivered_at.human }}</div>
            </div>
        </TableCell>
        <TableCell :clickable="true" @click="preview = true">
            <div class="text-left">{{ content.excerpt }}</div>
        </TableCell>
        <TableCell :clickable="true" @click="preview = true" class="w-20">
            <MediaFile v-if="content.media" :media="content.media" img-height="sm"/>
        </TableCell>
        <TableCell :clickable="true" @click="preview = true">
            <div class="flex flex-wrap gap-2">
                <Tag v-for="tag in item.tags" :key="tag.id" :item="tag" :removable="false" :editable="false"/>
            </div>
        </TableCell>
        <TableCell :clickable="true" @click="preview = true">
            <div class="flex gap-2">
                <Account v-for="account in item.accounts" :key="account.id" :provider="account.provider"
                         :img-url="account.image"
                         :active="true"/>
            </div>
        </TableCell>
        <TableCell>
            <PostItemActions :item-id="item.id" @onDelete="onDelete"/>
        </TableCell>

        <DialogModal :show="preview" :scrollableBody="true" @close="preview = false">
            <template #body>
                <PostPreviewProviders v-if="preview"
                                      :accounts="item.accounts"
                                      :selected-accounts="item.accounts.map(account => account.id)"
                                      :versions="item.versions"
                />
            </template>
            <template #footer>
                <template v-if="preview">
                    <div class="mr-2">
                        <PostItemActions :item-id="item.id" @onDelete="onDelete"/>
                    </div>
                    <SecondaryButton @click="preview = false">Close</SecondaryButton>
                </template>
            </template>
        </DialogModal>
    </TableRow>
</template>
