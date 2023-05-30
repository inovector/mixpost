<script setup>
import {computed} from "vue";
import usePost from "@/Composables/usePost";
import ExclamationIcon from "@/Icons/Exclamation.vue"

const {accountsHitTextLimit, accountsHitMediaLimit} = usePost();

const show = computed(() => {
    return accountsHitTextLimit.value.length !== 0 || accountsHitMediaLimit.value.length !== 0;
})

const resolveProvider = (provider) => {
    if (['facebook_page', 'facebook_group'].includes(provider)) {
        return 'facebook';
    }

    return provider;
}
</script>
<template>
    <div v-if="show"
         class="w-full flex items-center row-px py-xs md:py-md flex-row border-b border-gray-200 text-red-500 bg-red-50">
        <div class="w-8 h-8 mr-sm flex items-center">
            <ExclamationIcon/>
        </div>

        <div>
            <div v-if="accountsHitTextLimit">
                <p v-for="item in accountsHitTextLimit">
                    <span class="capitalize">{{ resolveProvider(item.provider) }}</span> can only fit {{ item.limit }}
                    characters. <span v-if="item.account_name">Check out the <span class="font-semibold">{{
                        item.account_name
                    }}</span> version.</span>
                </p>
            </div>

            <div v-if="accountsHitMediaLimit">
                <p v-for="item in accountsHitMediaLimit">
                    <span v-if="item.mixing.hit">
                        <span>You cannot mix video, gif and images on {{ resolveProvider(item.mixing.provider) }}.</span>
                    </span>
                    <span v-else>
                    <span v-if="item.photos.hit">
                        <span class="capitalize">{{
                                resolveProvider(item.photos.provider)
                            }}</span> supports up to {{ item.photos.limit }} images.
                    </span>
                    <span v-if="item.videos.hit">
                        <span class="capitalize">{{
                                resolveProvider(item.videos.provider)
                            }}</span> supports up to {{ item.videos.limit }} videos.
                    </span>
                    <span v-if="item.gifs.hit">
                        <span class="capitalize">{{
                                resolveProvider(item.gifs.provider)
                            }}</span> supports up to {{ item.gifs.limit }} gifs.
                    </span>
                </span>
                    <span v-if="item.account_name"> Check out the <span class="font-semibold">{{
                            item.account_name
                        }}</span> version.</span>
                </p>
            </div>
        </div>
    </div>
</template>
