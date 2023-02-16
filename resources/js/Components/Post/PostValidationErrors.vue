<script setup>
import {computed} from "vue";
import usePost from "@/Composables/usePost";
import ExclamationIcon from "@/Icons/Exclamation.vue"

const {accountsReachedTextLimit} = usePost();

const show = computed(() => {
    return accountsReachedTextLimit.value !== null;
})
</script>
<template>
    <div v-if="show"
         class="w-full flex items-center row-px py-md flex-row border-b border-gray-200 text-red-500 bg-red-50">
        <div class="w-8 h-8 mr-sm flex items-center">
            <ExclamationIcon/>
        </div>
        <div v-if="accountsReachedTextLimit">
            <p v-for="(item, _) in accountsReachedTextLimit">
                <span class="capitalize">{{ item.provider }}</span> can only fit {{ item.limit }} characters.
                <span v-if="item.account_name">Check out the <span class="font-semibold">{{ item.account_name }}</span> version.</span>
            </p>
        </div>
    </div>
</template>
