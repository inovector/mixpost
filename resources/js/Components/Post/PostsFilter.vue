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
import VerticallyScrollableContent from "@/Components/Surface/VerticallyScrollableContent.vue";
import FunnelIcon from "@/Icons/Funnel.vue";
import MagnifyingGlassIcon from "@/Icons/MagnifyingGlass.vue"
import XIcon from "@/Icons/X.vue"
import Checkbox from "@/Components//Form/Checkbox.vue";

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
            <label for="keyword" class="absolute top-0 left-0 ml-sm mt-xs">
                <MagnifyingGlassIcon class="text-stone-600"/>
            </label>
            <div v-if="modelValue.keyword" @click="modelValue.keyword = ''" tabindex="0" role="button"
                 class="absolute top-0 right-0 mr-xs mt-2.5">
                <XIcon
                    class="!w-5 !h-5 text-stone-600 hover:text-stone-800 transition-colors ease-in-out duration-200"/>
            </div>
        </div>

        <Dropdown width-classes="w-72" placement="bottom-end" :closeable-on-content="false">
            <template #trigger>
                <PrimaryButton size="md">
                    <FunnelIcon/>
                    <span class="ml-xs hidden sm:inline-block">Filters</span>
                </PrimaryButton>
            </template>

            <template #header>
                <PureButton @click="clear">Clear filter</PureButton>
            </template>

            <template #content>
                <VerticallyScrollableContent>
                    <div class="p-sm">
                        <div class="font-semibold">Labels</div>
                        <div class="mt-sm flex flex-wrap items-center gap-xs">
                            <template v-for="tag in tags" :key="tag.id">
                                <label class="flex items-center cursor-pointer">
                                    <Checkbox v-model:checked="modelValue.tags" :value="tag.id" number class="mr-1"/>
                                    <Tag :item="tag" :removable="false" :editable="false"/>
                                </label>
                            </template>
                        </div>
                    </div>

                    <div class="p-sm mt-sm">
                        <div class="font-semibold">Accounts</div>
                        <div class="mt-sm flex flex-wrap items-center gap-xs">
                            <template v-for="account in accounts" :key="account.id">
                                <label class="flex items-center cursor-pointer">
                                    <Checkbox v-model:checked="modelValue.accounts" :value="account.id" number class="mr-1"/>
                                    <Badge class="inline-flex items-center">
                                        <ProviderIcon :provider="account.provider" class="!w-4 !h-4 mr-xs"/>
                                        {{ account.name }}
                                    </Badge>
                                </label>
                            </template>
                        </div>
                    </div>
                </VerticallyScrollableContent>
            </template>
        </Dropdown>
    </div>
</template>
