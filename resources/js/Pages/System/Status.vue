<script setup>
import {Head} from '@inertiajs/vue3';
import useNotifications from "../../Composables/useNotifications";
import PageHeader from "../../Components/DataDisplay/PageHeader.vue";
import Panel from "../../Components/Surface/Panel.vue";
import Table from "../../Components/DataDisplay/Table.vue";
import TableRow from "../../Components/DataDisplay/TableRow.vue";
import TableCell from "../../Components/DataDisplay/TableCell.vue";
import Badge from "../../Components/DataDisplay/Badge.vue";
import PrimaryButton from "../../Components/Button/PrimaryButton.vue";
import Clipboard from "../../Icons/Clipboard.vue";

const props = defineProps({
    health: Object,
    tech: Object,
})

const {notify} = useNotifications();

const getBody = () => {
    let body = `## Describe your issue\n\n--- \n`;

    body += `## Health\n\n`;
    body += `**Environment**: ${props.health.env} \n`;
    body += `**Debug Mode**: ${props.health.debug ? 'Enabled' : 'Disabled'} \n`
    body += `**Horizon**: ${props.health.horizon_status} \n`
    body += `**Queue connection**: ${props.health.has_queue_connection ? 'Ok' : 'Not ok'} \n`
    body += `**Schedule**: ${props.health.last_scheduled_run.message} \n`

    body += `\n`;

    body += `## Technical Details:\n\n`;
    body += `**App directory**: ${props.tech.base_path} \n`;
    body += `**Upload Media Disk**: ${props.tech.disk} \n`;
    body += `*Log Channel**: ${props.tech.log_channel} \n`;
    body += `**Cache Driver**: ${props.tech.cache_driver} \n`;
    body += `**User agent**: ${props.tech.user_agent} \n`;
    body += `**FFmpeg**: ${props.ffmpeg_status} \n`;
    if (props.tech.versions.mysql) {
        body += `**MySql**: ${props.tech.versions.mysql} \n`;
    }
    body += `**PHP**: ${props.tech.versions.php} \n`;
    body += `**Laravel**: ${props.tech.versions.laravel} \n`;
    body += `**Horizon**: ${props.tech.versions.horizon} \n`;
    body += `**Mixpost Lite**: ${props.tech.versions.mixpost} \n`;

    return body;
}

const copyToClipboard = () => {
    navigator.clipboard.writeText(getBody())
        .then(() => {
            notify('success', 'System status information copied to clipboard')
        })
        .catch(() => {
            notify('error', 'Error copying status information to clipboard');
        });
}
</script>
<template>
    <Head title="Status"/>

    <div class="w-full mx-auto row-py">
        <PageHeader title="Status">
            <PrimaryButton @click="copyToClipboard" size="md">
                <Clipboard class="mr-xs"/>
                Copy info
            </PrimaryButton>
        </PageHeader>

        <div class="mt-lg row-px w-full">
            <Panel>
                <template #title>Health</template>

                <Table>
                    <template #body>
                        <TableRow :hoverable="true">
                            <TableCell>
                                <Badge :variant="health.env === 'production' ? 'success' : 'warning'">Environment</Badge>
                            </TableCell>
                            <TableCell>
                                {{ health.env }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell>
                                <Badge :variant="health.debug ? 'warning' : 'success'">Debug Mode</Badge>
                            </TableCell>
                            <TableCell>
                                {{ health.debug ? 'Enabled' : 'Disabled' }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell>
                                <Badge
                                    :variant="health.horizon_status === 'Active' ? 'success' : (health.horizon_status === 'Paused' ? 'warning' : 'error')">
                                    Horizon
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <span v-if="health.horizon_status === 'Inactive'">
                                    <span class="block">Inactive</span>
                                    Read the <a
                                    :href="`${$page.props.mixpost.docs_link}/lite/installation/laravel-package#5-install-horizon`">documentation</a>.
                                </span>
                                <span v-else>
                                    {{ health.horizon_status }}
                                </span>
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell>
                                <Badge :variant="health.has_queue_connection ? 'success'  : 'error'">
                                    Queue connection
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <span
                                    v-if="health.has_queue_connection">Queue connection settings for mixpost-redis exist.</span>
                                <span v-else>
                                    <span class="block">No valid <span class="font-medium">queue connection</span> found.</span>
                                    <span class="block">Configure a queue connection with the <span class="font-medium">mixpost-redis</span> key.</span>
                                     Read the <a
                                    :href="`${$page.props.mixpost.docs_link}/lite/installation/laravel-package#5-install-horizon`">documentation</a>.
                               </span>
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell>
                                <Badge :variant="health.last_scheduled_run.variant">Schedule</Badge>
                            </TableCell>
                            <TableCell>
                                {{ health.last_scheduled_run.message }}
                            </TableCell>
                        </TableRow>
                    </template>
                </Table>
            </Panel>

            <Panel class="mt-lg">
                <template #title>Technical details</template>

                <Table>
                    <template #body>
                        <TableRow :hoverable="true">
                            <TableCell class="font-medium">
                                App directory
                            </TableCell>
                            <TableCell>
                                {{ tech.base_path }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-medium">
                                Upload Media Disk
                            </TableCell>
                            <TableCell>
                                {{ tech.disk }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-medium">
                                Log Channel
                            </TableCell>
                            <TableCell>
                                {{ tech.log_channel }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-medium">
                                Cache Driver
                            </TableCell>
                            <TableCell>
                                {{ tech.cache_driver }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-medium">
                                User agent
                            </TableCell>
                            <TableCell>
                                {{ tech.user_agent }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-medium">
                                FFMpeg
                            </TableCell>
                            <TableCell>
                                {{ tech.ffmpeg_status }}
                            </TableCell>
                        </TableRow>
                        <template v-if="tech.versions.mysql">
                            <TableRow :hoverable="true">
                                <TableCell class="font-medium">
                                    MySql
                                </TableCell>
                                <TableCell>
                                    {{ tech.versions.mysql }}
                                </TableCell>
                            </TableRow>
                        </template>
                        <TableRow :hoverable="true">
                            <TableCell class="font-medium">
                                PHP
                            </TableCell>
                            <TableCell>
                                {{ tech.versions.php }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-medium">
                                Laravel
                            </TableCell>
                            <TableCell>
                                {{ tech.versions.laravel }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-medium">
                                Horizon
                            </TableCell>
                            <TableCell>
                                {{ tech.versions.horizon }}
                            </TableCell>
                        </TableRow>
                        <TableRow :hoverable="true">
                            <TableCell class="font-medium">
                                Mixpost Lite
                            </TableCell>
                            <TableCell>
                                {{ tech.versions.mixpost }}
                            </TableCell>
                        </TableRow>
                    </template>
                </Table>
            </Panel>
        </div>
    </div>
</template>
