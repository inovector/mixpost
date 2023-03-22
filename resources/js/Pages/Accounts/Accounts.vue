<script setup>
import {computed, ref} from "vue";
import {router} from '@inertiajs/vue3'
import {Head, Link} from '@inertiajs/vue3';
import useNotifications from "@/Composables/useNotifications";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Panel from "@/Components/Surface/Panel.vue";
import Modal from "@/Components/Modal/Modal.vue"
import ConfirmationModal from "@/Components/Modal/ConfirmationModal.vue"
import Account from "@/Components/Account/Account.vue"
import AddTwitterAccount from "@/Components/Account/AddTwitterAccount.vue"
import AddFacebookPage from "@/Components/Account/AddFacebookPage.vue"
import AddFacebookGroup from "@/Components/Account/AddFacebookGroup.vue"
import AddMastodonAccount from "@/Components/Account/AddMastodonAccount.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import DangerButton from "@/Components/Button/DangerButton.vue"
import Dropdown from "@/Components/Dropdown/Dropdown.vue"
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue"
import PlusIcon from "@/Icons/Plus.vue";
import EllipsisVerticalIcon from "@/Icons/EllipsisVertical.vue";
import RefreshIcon from "@/Icons/Refresh.vue";
import TrashIcon from "@/Icons/Trash.vue";
import Alert from "@/Components/Util/Alert.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import PureButton from "@/Components/Button/PureButton.vue";

const props = defineProps({
    has_service: {
        required: true,
        type: Object,
    }
})
const title = 'Social Accounts';

const {notify} = useNotifications();

const addAccountModal = ref(false);
const confirmationAccountDeletion = ref(null);
const accountIsDeleting = ref(false);

const anyUnconfiguredServices = computed(() => {
    return Object.keys(props.has_service).some((service) => props.has_service[service] !== true)
});

const updateAccount = (accountId) => {
    router.put(route('mixpost.accounts.update', {account: accountId}), {}, {
        preserveScroll: true,
        onSuccess(response) {
            if (response.props.flash.error) {
                return;
            }

            notify('success', 'The account has been refreshed');
        }
    });
}

const deleteAccount = () => {
    router.delete(route('mixpost.accounts.delete', {account: confirmationAccountDeletion.value}), {
        preserveScroll: true,
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

    <div class="w-full max-w-5xl mx-auto row-py">
        <PageHeader :title="title">
            <template #description>
                Connect a social account you'd like to manage.
            </template>
        </PageHeader>

        <div class="mt-lg row-px w-full">
            <div v-if="anyUnconfiguredServices" class="mb-md">
                <Alert variant="warning" :closeable="false" class="mb-md">
                    <p v-if="!$page.props.has_service.twitter">You have not configured Twitter service.</p>
                    <p v-if="!$page.props.has_service.facebook">You have not configured Facebook service.</p>
                    <p class="mt-xs italic">Click on the button below to configure the third-party services.</p>
                </Alert>

                <Link :href="route('mixpost.services.index')" class="inline-block">
                    <PrimaryButton>Configure services</PrimaryButton>
                </Link>
            </div>

            <div class="w-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
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
                                    <PureButton>
                                        <EllipsisVerticalIcon/>
                                    </PureButton>
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

    <ConfirmationModal :show="confirmationAccountDeletion !== null"
                       @close="closeConfirmationAccountDeletion"
                       variant="danger">
        <template #header>
            Delete account
        </template>
        <template #body>
            Are you sure you want to delete this account?
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

    <Modal :show="addAccountModal"
           :closeable="true"
           @close="addAccountModal = false">
        <div class="flex flex-col">
            <AddTwitterAccount v-if="$page.props.has_service.twitter"/>
            <AddFacebookPage v-if="$page.props.has_service.facebook"/>
            <AddFacebookGroup v-if="$page.props.has_service.facebook"/>
            <AddMastodonAccount/>
        </div>
    </Modal>
</template>
