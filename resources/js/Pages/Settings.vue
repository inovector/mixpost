<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import useNotifications from "@/Composables/useNotifications";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Panel from "@/Components/Surface/Panel.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Radio from "@/Components/Form/Radio.vue";
import Select from "@/Components/Form/Select.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Input from "../Components/Form/Input.vue";
import Error from "../Components/Form/Error.vue";

const props = defineProps(['settings', 'timezone_list'])

const form = useForm(props.settings);

const {notify} = useNotifications();

const save = () => {
    form.put(route('mixpost.settings.update'), {
        onSuccess() {
            notify('success', 'Settings have been saved');
        }
    });
}
</script>
<template>
    <Head title="Settings"/>

    <div class="row-py mb-2xl w-full mx-auto">
        <PageHeader title="Settings"/>

        <div class="row-px">
            <Panel>
                <template #title>Notifications</template>
                <template #description>
                    This email will receive notifications of lost account connections.
                </template>

                <HorizontalGroup class="mt-lg">
                    <template #title>Email</template>

                    <Input v-model="form.admin_email" type="email"  :error="form.errors.admin_email !== undefined"/>

                    <template #footer>
                        <Error :message="form.errors.admin_email"/>
                    </template>
                </HorizontalGroup>
            </Panel>

            <Panel class="mt-lg">
                <template #title>Time settings</template>

                <template #description>
                    The app will use these settings to display your calendar & analytics.
                </template>

                <HorizontalGroup>
                    <template #title>Timezone</template>

                    <div>
                        <Select v-model="form.timezone">
                            <optgroup v-for="(list, groupName) in timezone_list" :label="groupName">
                                <option v-for="(timezoneName,timezoneCode) in list" :value="timezoneCode">
                                    {{ timezoneName }}
                                </option>
                            </optgroup>
                        </Select>
                    </div>
                </HorizontalGroup>

                <HorizontalGroup class="mt-lg">
                    <template #title>Time format</template>

                    <div class="flex items-center space-x-sm">
                        <label>
                            <Radio v-model:checked="form.time_format" :value="12"/>
                            12 hour</label>
                        <label>
                            <Radio v-model:checked="form.time_format" :value="24"/>
                            24 hour</label>
                    </div>
                </HorizontalGroup>

                <HorizontalGroup class="mt-lg">
                    <template #title>First day of week</template>

                    <div class="flex items-center space-x-sm">
                        <label>
                            <Radio v-model:checked="form.week_starts_on" :value="0"/>
                            Sunday</label>
                        <label>
                            <Radio v-model:checked="form.week_starts_on" :value="1"/>
                            Monday</label>
                    </div>
                </HorizontalGroup>
            </Panel>

            <PrimaryButton @click="save" class="mt-lg">Save settings</PrimaryButton>
        </div>
    </div>
</template>
