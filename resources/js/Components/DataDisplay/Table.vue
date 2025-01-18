<template>
    <div class="flex flex-col overflow-x-hidden overflow-y-hidden">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 border-collapse">
                    <thead v-if="$slots.head">
                    <slot name="head"/>
                    </thead>
                    <tbody v-if="$slots.body" class="divide-y divide-gray-100">
                    <slot name="body"/>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
