<script setup>
import {ref} from "vue";
import {Inertia} from '@inertiajs/inertia'
import {Head} from '@inertiajs/inertia-vue3';
import useNotifications from "@/Composables/useNotifications";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Panel from "@/Components/Surface/Panel.vue";
import Modal from "@/Components/Modal/Modal.vue"
import ConfirmationModal from "@/Components/Modal/ConfirmationModal.vue"
import Account from "@/Components/Account/Account.vue"
import AddTwitterAccount from "@/Components/Account/AddTwitterAccount.vue"
import AddFacebookAccount from "@/Components/Account/AddFacebookAccount.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import DangerButton from "@/Components/Button/DangerButton.vue"
import Dropdown from "@/Components/Dropdown/Dropdown.vue"
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue"
import PlusIcon from "@/Icons/Plus.vue";
import EllipsisVerticalIcon from "@/Icons/EllipsisVertical.vue";
import RefreshIcon from "@/Icons/Refresh.vue";
import TrashIcon from "@/Icons/Trash.vue";

const title = 'Social Accounts';

const {notify} = useNotifications();

const addAccountModal = ref(false);
const confirmationAccountDeletion = ref(null);
const accountIsDeleting = ref(false);

const updateAccount = (accountId) => {
    Inertia.put(route('mixpost.accounts.update', {account: accountId}), {}, {
        onSuccess() {
            notify('success', 'Account has been refreshed');
        }
    });
}

const deleteAccount = () => {
    Inertia.delete(route('mixpost.accounts.delete', {account: confirmationAccountDeletion.value}), {
        onStart() {
            accountIsDeleting.value = true;
        },
        onSuccess() {
            confirmationAccountDeletion.value = null;
            notify('success', 'Account deleted');
        },
        onFinish() {
            accountIsDeleting.value = false;
        },
    });
}

const closeConfirmationAccountDeletion = () => {
    if (accountIsDeleting.value) {
        return;
    }

    confirmationAccountDeletion.value = null
}
</script>
<template>
    <Head :title="title"/>

    <div class="max-w-5xl mx-auto row-py">
        <PageHeader :title="title">
            <template #description>
                Connect a social account you'd like to manage.
            </template>
        </PageHeader>

        <div class="mt-lg row-px">
            <div class="grid grid-cols-4 gap-6">
                <button @click="addAccountModal = true"
                        class="border border-indigo-800 rounded-lg hover:border-indigo-500 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                    <span class="block p-lg">
                        <span class="flex flex-col justify-center items-center">
                            <PlusIcon class="w-7 h-7"/>
                            <span class="mt-xs text-lg">Add account</span>
                        </span>
                    </span>
                </button>
                <template v-for="account in $page.props.accounts" :key="account.id">
                    <Panel class="relative">
                        <div class="absolute top-0 right-0 mt-sm mr-sm">
                            <Dropdown width-classes="w-32">
                                <template #trigger>
                                    <SecondaryButton size="xs">
                                        <EllipsisVerticalIcon/>
                                    </SecondaryButton>
                                </template>

                                <template #content>
                                    <DropdownItem @click="updateAccount(account.id)" as="button">
                                        <RefreshIcon class="!w-5 !h-5 mr-1"/>
                                        Refresh
                                    </DropdownItem>
                                    <DropdownItem @click="confirmationAccountDeletion = account.id" as="button">
                                        <TrashIcon class="!w-5 !h-5 mr-1 text-red-500"/>
                                        Delete
                                    </DropdownItem>
                                </template>
                            </Dropdown>
                        </div>

                        <div class="flex flex-col justify-center">
                            <Account
                                size="lg"
                                :img-url="account.image"
                                :provider="account.provider"
                                :active="true"
                            />

                            <div class="mt-sm font-semibold text-center">{{ account.name }}</div>
                            <div class="mt-1 text-center text-stone-800">Added: {{ account.created_at }}</div>
                        </div>
                    </Panel>
                </template>
            </div>
        </div>
    </div>

    <Modal :show="addAccountModal"
                  :closeable="true"
                  @close="addAccountModal = false">
        <div class="flex flex-col">
            <AddTwitterAccount/>
            <AddFacebookAccount/>
        </div>
    </Modal>

    <ConfirmationModal :show="confirmationAccountDeletion !== null"
                              @close="closeConfirmationAccountDeletion"
                              variant="danger">
        <template #header>
            Delete account
        </template>
        <template #body>
            Are you sure you would like to delete this account?
        </template>
        <template #footer>
            <SecondaryButton @click="closeConfirmationAccountDeletion" :disabled="accountIsDeleting"
                                    class="mr-xs">Cancel
            </SecondaryButton>
            <DangerButton @click="deleteAccount" :is-loading="accountIsDeleting"
                                 :disabled="accountIsDeleting">Delete
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>
