<template>
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="table-fixed min-w-full divide-y divide-gray-200 border-collapse">
                        <thead>
                        <slot name="head"/>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        <slot name="body"/>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
