<script setup>
import {ref, watch} from "vue";
import {Head} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {cloneDeep, pickBy, throttle} from "lodash";
import NProgress from 'nprogress'
import PageHeader from '@/Components/DataDisplay/PageHeader.vue';
import PostsFilter from '@/Components/Post/PostsFilter.vue';
import Tabs from "@/Components/Navigation/Tabs.vue"
import Tab from "@/Components/Navigation/Tab.vue"
import Panel from "@/Components/Surface/Panel.vue";
import Checkbox from "@/Components/Form/Checkbox.vue";
import Table from "@/Components/DataDisplay/Table.vue";
import TableRow from "@/Components/DataDisplay/TableRow.vue";
import TableCell from "@/Components/DataDisplay/TableCell.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue";
import PostItem from "@/Components/Post/PostItem.vue";

const props = defineProps({
    filter: {
        type: Object,
        default: {}
    },
    posts: {
        type: Object,
    }
});

const items = ref(props.posts.data.slice())
const pagination = ref(props.posts.links);
const isLoading = ref(false);
const filter = ref({
    keyword: props.filter.keyword,
    status: props.filter.status,
    tags: props.filter.tags,
    accounts: props.filter.accounts
})

const loadMore = () => {
    if (isLoading.value) return;

    isLoading.value = true;
    NProgress.start();

    axios.get(props.posts.links.next).then((response) => {
        items.value = [...items.value, ...response.data.data];
        pagination.value = response.data.links;
    }).finally(() => {
        isLoading.value = false;
        NProgress.done();
    })
}

watch(() => cloneDeep(filter.value), throttle(() => {
    Inertia.get(route('mixpost.posts.index'), pickBy(filter.value), {
        preserveState: true,
        onSuccess() {
            items.value = props.posts.data;
            pagination.value = props.posts.links
        }
    });
}, 300))
</script>
<template>
    <Head title="Posts"/>

    <div class="default-y-padding">
        <PageHeader title="Posts">
            <PostsFilter v-model="filter"/>
        </PageHeader>

        <div class="w-full default-x-padding">
            <Tabs>
                <Tab @click="filter.status = null" :active="!filter.status">All</Tab>
                <Tab @click="filter.status = 'draft'" :active="filter.status === 'draft'">Drafts</Tab>
                <Tab @click="filter.status = 'scheduled'" :active="filter.status === 'scheduled'">Scheduled</Tab>
                <Tab @click="filter.status = 'published'" :active="filter.status === 'published'">Published</Tab>
            </Tabs>
        </div>

        <div class="w-full default-x-padding default-t-margin">
            <Panel :with-padding="false">
                <Table>
                    <template #head>
                        <TableRow>
                            <TableCell component="th" scope="col" class="w-10">
                                <Checkbox/>
                            </TableCell>
                            <TableCell component="th" scope="col">Status</TableCell>
                            <TableCell component="th" scope="col">Content</TableCell>
                            <TableCell component="th" scope="col" class="w-20">Media</TableCell>
                            <TableCell component="th" scope="col">Labels</TableCell>
                            <TableCell component="th" scope="col">Accounts</TableCell>
                            <TableCell component="th" scope="col"/>
                        </TableRow>
                    </template>
                    <template #body>
                        <template v-for="item in items" :key="item.id">
                            <PostItem :item="item" :accounts="filter.accounts"/>
                        </template>
                    </template>
                </Table>
            </Panel>

            <div v-if="pagination.next" class="flex justify-center default-t-margin">
                <SecondaryButton @click="loadMore" :disabled="isLoading">Load more</SecondaryButton>
            </div>
        </div>
    </div>
</template>
