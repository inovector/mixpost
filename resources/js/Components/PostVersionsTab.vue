<script setup>
import {computed} from "vue";
import useProviderIcon from "@/Composables/useProviderIcon";
import usePostVersions from "@/Composables/usePostVersions";
import Dropdown from "@/Components/Dropdown.vue";
import MixpostDropdownItem from "@/Components/DropdownItem.vue";
import MixpostAccount from "@/Components/Account.vue"
import MixpostTabItem from "@/Components/TabItem.vue"
import MixpostPureButton from "@/Components/PureButton.vue"
import PlusIcon from "@/Icons/Plus.vue"

const props = defineProps({
    versions: {
        required: true,
        type: Array
    },
    activeVersion: {
        type: [Number, null],
        default: null,
    },
    accounts: {
        required: true,
        type: Array
    },
    selectedAccounts: {
        required: true,
        type: Array
    }
})

defineEmits(['add', 'select']);

const {providerIconComponentFnc} = useProviderIcon();

// Get selected accounts and those accounts that don't have a version
const availableAccounts = computed(() => {
    return props.accounts.filter((account) => {
        return props.selectedAccounts.includes(account.id) && !props.versions.map(version => version.account_id).includes(account.id);
    })
});

const {getOriginalVersion} = usePostVersions();

const versionsWithAccountData = computed(() => {
    const defaultVersion = {...getOriginalVersion(props.versions), ...{account: {name: 'Original'}}};

    const versionsBelongsToAccount = props.versions.map((version) => {
        const account = props.accounts.find(account => account.id === version.account_id);

        if (account !== undefined) {
            return {...version, ...{account}}
        }

        return null;
    }).filter((version) => version);

    return [...[defaultVersion], ...versionsBelongsToAccount]
});
</script>
<template>
    <div class="flex flex-wrap items-start">
        <template v-for="version in versionsWithAccountData" :key="version.account_id">
            <MixpostTabItem @click="$emit('select', version.account_id)" :active="activeVersion === version.account_id">
                <component
                    v-if="!version.is_original"
                    :is="providerIconComponentFnc(version.account.provider)"
                    :class="['!w-4', '!h-4', ('text-' + version.account.provider)]"
                    class="mr-2"
                />
                <span>{{ version.account.name }}</span>
            </MixpostTabItem>
        </template>

        <Dropdown>
            <template #trigger>
                <MixpostPureButton v-if="availableAccounts.length" v-tooltip="'Create version'">
                    <PlusIcon/>
                </MixpostPureButton>
            </template>

            <template #header>
                <div class="font-semibold">Create version for</div>
            </template>

            <template #content>
                <template v-for="account in availableAccounts">
                    <MixpostDropdownItem @click="$emit('add', account.id)" as="button" class="!py-3">
                      <span class="mr-3">
                          <MixpostAccount :provider="account.provider"
                                          :img-url="account.image"
                                          :active="true"/>
                      </span>
                        <span class="text-left">{{ account.name }}</span>
                    </MixpostDropdownItem>
                </template>
            </template>
        </Dropdown>
    </div>
</template>
