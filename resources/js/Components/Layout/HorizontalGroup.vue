<template>
    <div class="flex justify-between items-center">
        <div class="w-full flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div v-if="$slots.title"
                 class="font-semibold sm:mr-xs">
                <slot name="title"/>
            </div>

            <div v-if="$slots.default" class="mt-xs sm:mt-0">
                <slot/>
            </div>
        </div>
    </div>
</template>
