<script setup>
import {computed, onMounted, ref} from "vue";
import {Head, router} from '@inertiajs/vue3';
import PageHeader from "../../Components/DataDisplay/PageHeader.vue";
import Panel from "../../Components/Surface/Panel.vue";
import Tabs from "../../Components/Navigation/Tabs.vue";
import Tab from "../../Components/Navigation/Tab.vue";
import Alert from "../../Components/Util/Alert.vue";
import Textarea from "../../Components/Form/Textarea.vue";
import PrimaryButton from "../../Components/Button/PrimaryButton.vue";
import DangerButton from "../../Components/Button/DangerButton.vue";
import Document from "../../Icons/Document.vue";
import NoResult from "../../Components/Util/NoResult.vue";

const props = defineProps({
    logs: {
        required: true,
        type: Array
    }
})

const activeLog = ref(null);

onMounted(() => {
    if (props.logs.length) {
        activeLog.value = props.logs[0].name;
    }
});

const activeLogItem = computed(() => {
    const find = props.logs.find(log => log.name === activeLog.value);

    if (!find) {
        return false;
    }

    return find;
});

const clear = () => {
    router.delete(route('mixpost.system.logs.clear'), {
        data: {
            filename: activeLogItem.value.name
        }
    });
}
</script>
<template>
    <Head title="System Logs"/>

    <div class="w-full mx-auto row-py">
        <PageHeader title="System Logs"/>

        <div class="w-full row-px">
            <Tabs>
                <template v-for="log in logs" :key="log.name">
                    <Tab @click="activeLog = log.name" :active="activeLog === log.name">
                        <template #icon>
                            <Document/>
                        </template>

                        {{ log.name }}
                    </Tab>
                </template>
            </Tabs>
        </div>

        <div class="mt-lg row-px w-full">
            <Panel class="mt-lg">
                <template v-if="logs.length">
                    <template v-if="activeLogItem">
                        <Alert v-if="activeLogItem.error" :closeable="false" variant="warning" class="mb-md">
                            {{ activeLogItem.error }}
                        </Alert>

                        <Textarea :value="activeLogItem.contents" class="h-96" readonly/>
                    </template>

                    <div class="mt-md">
                        <a :href="route('mixpost.system.logs.download', {filename: activeLogItem.name})"
                           target="_blank">
                            <PrimaryButton class="mr-xs rtl:mr-0 rtl:ml-xs">Download</PrimaryButton>
                        </a>
                        <DangerButton @click="clear">Clean up</DangerButton>
                    </div>
                </template>
                <NoResult v-else>
                    There are no logs.
                </NoResult>
            </Panel>
        </div>
    </div>
</template>
