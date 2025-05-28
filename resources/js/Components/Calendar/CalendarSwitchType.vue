<script setup>
import {computed, inject} from "vue";
import Dropdown from "@/Components/Dropdown/Dropdown.vue";
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import ChevronDownIcon from "@/Icons/ChevronDown.vue"
import CalendarIcon from "@/Icons/Calendar.vue"

const calendarType = inject('calendarType');

const typeName = computed(() => {
    return {
        'month': 'Month',
        'week': 'Week'
    }[calendarType.value]
});

const select = (type) => {
    calendarType.value = type;
}
</script>
<template>
    <Dropdown width-classes="w-32" placement="bottom">
        <template #trigger>
            <SecondaryButton size="sm">
                <span class="inline-block mr-xs">{{ typeName }}</span>
                <ChevronDownIcon/>
            </SecondaryButton>
        </template>

        <template #content>
            <DropdownItem as="button" @click="select('month')">
                <CalendarIcon class="w-5! h-5! mr-1"/>
                Month
            </DropdownItem>

            <DropdownItem as="button" @click="select('week')">
                <CalendarIcon class="w-5! h-5! mr-1"/>
                Week
            </DropdownItem>
        </template>
    </Dropdown>
</template>
