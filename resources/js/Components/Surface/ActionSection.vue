<script setup>
import Panel from "./Panel.vue";
</script>
<template>
    <div class="md:grid md:grid-cols-3 md:gap-lg">
        <div class="md:col-span-1 flex justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    <slot name="title"/>
                </h3>

                <p class="mt-xs text-gray-600 dark:text-gray-400">
                    <slot name="description"/>
                </p>
            </div>
        </div>

        <div class="mt-lg md:mt-0 md:col-span-2">
            <Panel>
                <slot/>
            </Panel>
        </div>
    </div>
</template>
