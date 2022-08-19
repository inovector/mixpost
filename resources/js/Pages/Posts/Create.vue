<script setup>
import {ref} from "vue";
import {Head, useForm} from '@inertiajs/inertia-vue3';
import useMounted from "@/Composables/useMounted";
import usePostVersions from "@/Composables/usePostVersions";
import MixpostPageHeader from "@/Components/PageHeader.vue";
import MixpostPostContext from "@/Context/PostContext.vue";
import MixpostPostForm from "@/Components/PostForm.vue";
import MixpostPostActions from "@/Components/PostActions.vue";
import MixpostPostPreviewProviders from "@/Components/PostPreviewProviders.vue"
import MixpostSecondaryButton from "@/Components/SecondaryButton.vue"
import EyeIcon from "@/Icons/Eye.vue"
import EyeOffIcon from "@/Icons/EyeOff.vue"

const {isMounted} = useMounted();
const showPreview = ref(false);

const {versionObject} = usePostVersions();

const form = useForm({
    accounts: [],
    versions: [versionObject(0, true)],
    date: '',
    time: ''
});


const save = (event) => {
    console.log(event);
}
</script>
<template>
    <Head title="Create a post"/>

    <MixpostPostContext>
        <div class="flex flex-col grow h-full">
            <div class="flex flex-row h-full overflow-y-auto">
                <div class="w-full md:w-3/5 h-full flex flex-col overflow-x-hidden overflow-y-auto">
                    <div class="default-y-padding">
                        <MixpostPageHeader title="Create a post">
                            <div class="flex items-center">
                                <div class="w-4 h-4 mr-2 rounded-full bg-lime-500"></div>
                                <div>Saved</div>
                            </div>
                        </MixpostPageHeader>

                        <div class="w-full max-w-7xl mx-auto default-x-padding">
                            <MixpostPostForm :form="form" :accounts="$page.props.accounts"/>
                        </div>
                    </div>
                </div>
                <div :class="{'translate-x-0': showPreview, 'translate-x-full md:translate-x-0': !showPreview}"
                     class="fixed md:relative w-full md:w-2/5 h-full overflow-x-hidden overflow-y-auto flex flex-col border-l border-gray-200 bg-stone-500 transition-transform ease-in-out duration-200">
                    <Teleport v-if="isMounted && form.accounts.length" to="#navRightButton">
                        <MixpostSecondaryButton @click="showPreview = !showPreview" size="xs">
                            <span class="mr-2">
                                <EyeOffIcon v-if="showPreview"/>
                                <EyeIcon v-else/>
                            </span>
                            <span>Preview</span>
                        </MixpostSecondaryButton>
                    </Teleport>

                    <div class="py-10">
                        <MixpostPageHeader title="Preview"/>

                        <div class="default-x-padding">
                            <MixpostPostPreviewProviders :accounts="$page.props.accounts"
                                                         :selected-accounts="form.accounts"
                                                         :versions="form.versions"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <MixpostPostActions :form="form" @submit="save"/>
        </div>
    </MixpostPostContext>
</template>
