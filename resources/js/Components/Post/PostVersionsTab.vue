<script setup>
import {computed, ref} from "vue";
import usePost from "@/Composables/usePost";
import usePostVersions from "@/Composables/usePostVersions";
import Dropdown from "@/Components/Dropdown/Dropdown.vue";
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue";
import Account from "@/Components/Account/Account.vue"
import ProviderIcon from "@/Components/Account/ProviderIcon.vue";
import Tabs from "@/Components/Navigation/Tabs.vue";
import Tab from "@/Components/Navigation/Tab.vue"
import ConfirmationModal from "@/Components/Modal/ConfirmationModal.vue"
import VerticallyScrollableContent from "@/Components/Surface/VerticallyScrollableContent.vue";
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import DangerButton from "@/Components/Button/DangerButton.vue"
import PureButton from "@/Components/Button/PureButton.vue"
import PlusIcon from "@/Icons/Plus.vue"
import XIcon from "@/Icons/X.vue"

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

const emit = defineEmits(['add', 'remove', 'select']);

const {editAllowed} = usePost();

// Get selected accounts and those accounts that don't have a version
const availableAccounts = computed(() => {
    return props.accounts.filter((account) => {
        return props.selectedAccounts.includes(account.id) && !props.versions.map(version => version.account_id).includes(account.id);
    })
});

const nameOfLastAvailableAccount = computed(() => {
    if (availableAccounts.value.length === 1) {
        return availableAccounts.value[0].name;
    }

    return null;
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

// Remove version
const confirmationRemoval = ref(null);

const closeConfirmationRemoval = () => {
    confirmationRemoval.value = null;
}
const remove = () => {
    emit('remove', confirmationRemoval.value.account_id);
    closeConfirmationRemoval();
}
</script>
<template>
    <div>
        <div class="flex flex-wrap items-start">
            <Tabs class="mr-xs">
                <template v-for="(version, index) in versionsWithAccountData" :key="version.account_id">
                    <Tab @click="$emit('select', version.account_id)" :active="activeVersion === version.account_id"
                         :tab-index="index" class="relative mb-xs group">
                        <ProviderIcon v-if="!version.is_original" :provider="version.account.provider"
                                      :class="['w-4!', 'h-4!']" class="mr-xs"/>
                        <span v-if="version.is_original && nameOfLastAvailableAccount"
                              v-tooltip="nameOfLastAvailableAccount"
                              class="mr-xs">{{ version.account.name }}</span>

                        <span v-else class="mr-xs">{{ version.account.name }}</span>

                        <div v-if="!version.is_original"
                             class="absolute hidden group-hover:flex items-center top-0 right-0 pb-2 pl-0.5 h-full bg-white">
                            <button @click.prevent.stop="confirmationRemoval = version"
                                    class="inline-flex text-gray-300 group-hover:text-gray-500 hover:text-red-500! transition-colors ease-in-out duration-200">
                                <XIcon class="w-4! h-4!"/>
                            </button>
                        </div>
                    </Tab>
                </template>
            </Tabs>

            <template v-if="editAllowed && availableAccounts.length > 1">
                <Dropdown width-classes="w-64">
                    <template #trigger>
                        <PureButton v-tooltip="'Create version'">
                            <PlusIcon/>
                        </PureButton>
                    </template>

                    <template #header>
                        <div class="font-semibold">Create version for</div>
                    </template>

                    <template #content>
                        <VerticallyScrollableContent max-height="xl">
                            <template v-for="account in availableAccounts">
                                <DropdownItem @click="$emit('add', account.id)" as="button">
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
            </template>
        </div>

        <ConfirmationModal :show="confirmationRemoval !== null"
                           @close="closeConfirmationRemoval"
                           variant="danger">
            <template #header>
                Remove version
            </template>
            <template #body>
              <span v-if="confirmationRemoval">
                  Are you sure you would like to delete version for  <span class="capitalize">[{{
                      confirmationRemoval.account.provider
                  }}]</span> {{ confirmationRemoval.account.name }}?
              </span>
            </template>
            <template #footer>
                <SecondaryButton @click="closeConfirmationRemoval" class="mr-xs">Cancel</SecondaryButton>
                <DangerButton @click="remove">Remove</DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
