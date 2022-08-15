<script setup>
import {Head, useForm} from '@inertiajs/inertia-vue3';
import MixpostPageHeader from "@/Components/PageHeader.vue";
import MixpostPostContext from "@/Context/PostContext.vue";
import PostForm from "@/Components/PostForm.vue";
import MixpostPostPreviewProviders from "@/Components/PostPreviewProviders.vue"

const form = useForm({
    accounts: [],
    body: '',
    date: '',
    time: ''
});


const save = (event) => {
    console.log(event);
}
</script>
<template>
    <Head title="Create new post"/>

    <MixpostPostContext>
        <div class="flex flex-row h-full">
            <div class="w-3/5 h-full flex flex-col overflow-y-auto">
                <div class="default-y-padding">
                    <MixpostPageHeader title="Create new post">
                        <div class="flex items-center">
                            <div class="w-4 h-4 mr-2 rounded-full bg-lime-500"></div>
                            <div>Saved</div>
                        </div>
                    </MixpostPageHeader>

                    <div class="w-full max-w-7xl mx-auto default-x-padding">
                        <PostForm :form="form" :accounts="$page.props.accounts" @submit="save"/>
                    </div>
                </div>
            </div>
            <div class="w-2/5 h-full border-l border-gray-200">
                <div class="py-10">
                    <MixpostPageHeader title="Preview"/>

                    <div class="default-x-padding">
                        <MixpostPostPreviewProviders :accounts="$page.props.accounts"
                                                     :selected-accounts="form.accounts"
                                                     :body="form.body"
                        />
                    </div>
                </div>
            </div>
        </div>
    </MixpostPostContext>
</template>
