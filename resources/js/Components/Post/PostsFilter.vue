<script setup>
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";
import Input from "@/Components/Form/Input.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Dropdown from "@/Components/Dropdown/Dropdown.vue";
import Tag from "@/Components/DataDisplay/Tag.vue";
import ProviderIcon from "@/Components/Account/ProviderIcon.vue";
import PureButton from "@/Components/Button/PureButton.vue";
import Badge from "@/Components/DataDisplay/Badge.vue";
import FunnelIcon from "@/Icons/Funnel.vue";
import MagnifyingGlassIcon from "@/Icons/MagnifyingGlass.vue"
import XIcon from "@/Icons/X.vue"

const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

const accounts = computed(() => {
    return usePage().props.value.accounts;
})

const tags = computed(() => {
    return usePage().props.value.tags;
})

const clear = () => {
    emit('update:modelValue', Object.assign(props.modelValue, {
        keyword: '',
        tags: [],
        accounts: []
    }))
}
</script>
<template>
    <div class="flex items-center">
        <div class="relative mx-2">
            <Input type="text" v-model="modelValue.keyword" id="keyword" placeholder="Search by keyword"
                   class="w-full pl-11 pr-11"/>
            <label for="keyword" class="absolute top-0 left-0 ml-3 mt-2">
                <MagnifyingGlassIcon class="text-stone-600"/>
            </label>
            <div v-if="modelValue.keyword" @click="modelValue.keyword = ''" tabindex="0" role="button"
                 class="absolute top-0 right-0 mr-2 mt-2.5">
                <XIcon
                    class="!w-5 !h-5 text-stone-600 hover:text-stone-800 transition-colors ease-in-out duration-200"/>
            </div>
        </div>

        <Dropdown width-classes="w-72" placement="bottom-end" :closeable-on-content="false">
            <template #trigger>
                <PrimaryButton size="md">
                    <FunnelIcon/>
                    <span class="ml-2 hidden sm:inline-block">Filters</span>
                </PrimaryButton>
            </template>

            <template #header>
                <PureButton @click="clear">Clear filter</PureButton>
            </template>

            <template #content>
                <div class="p-3">
                    <div>
                        <div class="font-semibold">Labels</div>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <template v-for="tag in tags" :key="tag.id">
                                <button>
                                    <Tag :item="tag" :removable="false" :editable="false"/>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="font-semibold">Accounts</div>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <template v-for="account in accounts" :key="account.id">
                                <button>
                                    <Badge class="inline-flex items-center">
                                        <ProviderIcon :provider="account.provider" class="!w-4 !h-4 mr-2"/>
                                        {{ account.name }}
                                    </Badge>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </Dropdown>
    </div>
</template>
