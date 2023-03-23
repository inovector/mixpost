<template>
    <div>
        <div class="flex justify-between items-center">
            <div class="w-full flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div v-if="$slots.title"
                     class="flex justify-start w-full font-semibold sm:mr-xs">
                    <slot name="title"/>
                </div>

                <div v-if="$slots.default" class="w-full flex justify-start mt-xs sm:mt-0">
                    <slot/>
                </div>
            </div>
        </div>
        <slot name="footer"/>
    </div>
</template>
