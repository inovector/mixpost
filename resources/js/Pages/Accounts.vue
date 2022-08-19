<script setup>
import {ref} from "vue";
import {Inertia} from '@inertiajs/inertia'
import {Head} from '@inertiajs/inertia-vue3';
import useNotifications from "@/Composables/useNotifications";
import MixpostPageHeader from "@/Components/PageHeader.vue";
import MixpostPanel from "@/Components/Panel.vue";
import MixpostModal from "@/Components/Modal.vue"
import MixpostConfirmationModal from "@/Components/ConfirmationModal.vue"
import MixpostAccount from "@/Components/Account.vue"
import MixpostAddTwitterAccount from "@/Components/AddTwitterAccount.vue"
import MixpostAddFacebookAccount from "@/Components/AddFacebookAccount.vue"
import MixpostSecondaryButton from "@/Components/SecondaryButton.vue"
import MixpostDangerButton from "@/Components/DangerButton.vue"
import Dropdown from "@/Components/Dropdown.vue"
import DropdownItem from "@/Components/DropdownItem.vue"
import PlusIcon from "@/Icons/Plus.vue";
import DotsVerticalIcon from "@/Icons/DotsVertical.vue";
import RefreshIcon from "@/Icons/Refresh.vue";
import TrashIcon from "@/Icons/Trash.vue";

const title = 'Social Accounts';

const {notify} = useNotifications();

const addAccountModal = ref(false);
const confirmingAccountDeletion = ref(null);
const accountIsDeleting = ref(false);

const updateAccount = (accountId) => {
    Inertia.put(route('mixpost.accounts.update', {account: accountId}), {}, {
        onSuccess() {
            notify('success', 'Account has been refreshed');
        }
    });
}

const deleteAccount = () => {
    Inertia.delete(route('mixpost.accounts.delete', {account: confirmingAccountDeletion.value}), {
        onStart() {
            accountIsDeleting.value = true;
        },
        onSuccess() {
            confirmingAccountDeletion.value = null;
            notify('success', 'Account deleted');
        },
        onFinish() {
            accountIsDeleting.value = false;
        },
    });
}

const closeConfirmingAccountDeletion = () => {
    if (accountIsDeleting.value) {
        return;
    }

    confirmingAccountDeletion.value = null
}
</script>
<template>
    <Head :title="title"/>

    <div class="max-w-5xl mx-auto default-y-padding">
        <MixpostPageHeader :title="title">
            <template #description>
                Connect a social account you'd like to manage.
            </template>
        </MixpostPageHeader>

        <div class="mt-6 default-x-padding">
            <div class="grid grid-cols-4 gap-6">
                <template v-for="account in $page.props.accounts" :key="account.id">
                    <MixpostPanel class="relative">
                        <div class="absolute top-0 right-0 mt-3 mr-3">
                            <Dropdown width-classes="w-32">
                                <template #trigger>
                                    <MixpostSecondaryButton size="xs">
                                        <DotsVerticalIcon/>
                                    </MixpostSecondaryButton>
                                </template>

                                <template #content>
                                    <DropdownItem @click="updateAccount(account.id)" as="button">
                                        <RefreshIcon class="!w-4 !h-4 mr-1"/>
                                        Refresh
                                    </DropdownItem>
                                    <DropdownItem @click="confirmingAccountDeletion = account.id" as="button">
                                        <TrashIcon class="!w-4 !h-4 mr-1 text-red-500"/>
                                        Delete
                                    </DropdownItem>
                                </template>
                            </Dropdown>
                        </div>

                        <div class="flex flex-col justify-center">
                            <MixpostAccount
                                size="lg"
                                :img-url="account.image"
                                :provider="account.provider"
                                :active="true"
                            />

                            <div class="mt-3 font-semibold text-center">{{ account.name }}</div>
                            <div class="mt-1 text-center text-stone-800">Added: {{ account.created_at }}</div>
                        </div>
                    </MixpostPanel>
                </template>

                <button @click="addAccountModal = true"
                        class="border border-indigo-800 rounded-lg hover:border-indigo-500 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                    <span class="block p-6">
                        <span class="flex flex-col justify-center items-center">
                            <PlusIcon class="w-7 h-7"/>
                            <span class="mt-2 text-lg">Add account</span>
                        </span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <MixpostModal :show="addAccountModal"
                  :closeable="true"
                  @close="addAccountModal = false">
        <div class="flex flex-col">
            <MixpostAddTwitterAccount/>
            <MixpostAddFacebookAccount/>
        </div>
    </MixpostModal>

    <MixpostConfirmationModal :show="confirmingAccountDeletion !== null"
                              @close="closeConfirmingAccountDeletion"
                              variant="danger">
        <template #header>
            Delete account
        </template>
        <template #body>
            Are you sure you would like to delete this account?
        </template>
        <template #footer>
            <MixpostSecondaryButton @click="closeConfirmingAccountDeletion" :disabled="accountIsDeleting"
                                    class="mr-2">Cancel
            </MixpostSecondaryButton>
            <MixpostDangerButton @click="deleteAccount" :is-loading="accountIsDeleting"
                                 :disabled="accountIsDeleting">Delete
            </MixpostDangerButton>
        </template>
    </MixpostConfirmationModal>
</template>
