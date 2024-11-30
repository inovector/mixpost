<script setup>
import {isEmpty} from "lodash";
import usePostValidator from "@/Composables/usePostValidator";
import ExclamationIcon from "@/Icons/Exclamation.vue"

const {validationPassed, errors} = usePostValidator();
</script>
<template>
    <div v-if="!validationPassed"
         class="w-full flex items-center row-px py-xs md:py-md flex-row border-b border-gray-200 text-red-500 bg-red-50">
        <div class="w-8 h-8 mr-sm flex items-center">
            <ExclamationIcon/>
        </div>

        <div v-if="!isEmpty(errors)">
            <ul class="list-disc pl-xs">
                <template v-for="group in errors">
                    <li v-for="error in group">
                        {{ error }}
                    </li>
                </template>
            </ul>
        </div>
    </div>
</template>
