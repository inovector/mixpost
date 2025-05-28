<script setup>
import {ref} from "vue";
import useAuth from "@/Composables/useAuth";
import Dropdown from "@/Components/Dropdown/Dropdown.vue";
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue";
import ArrowRightOnRectangleIcon from "@/Icons/ArrowRightOnRectangle.vue";
import UserCircle from "../../Icons/UserCircle.vue";
import PencilSquare from "../../Icons/PencilSquare.vue";

const {user} = useAuth();

const open = ref(false);
</script>
<template>
    <Dropdown width-classes="w-64" placement="top-start">
        <template #trigger>
            <div role="button" class="w-full flex items-center">
                <div class="mr-sm">
                    <UserCircle class="w-9! h-9! text-gray-400"/>
                </div>
                <div class="w-[calc(100%-3rem)]">
                    <div class="truncate">{{ user.name }}</div>
                </div>
            </div>
        </template>

        <template #content>
            <DropdownItem :href="route('mixpost.profile.index')">
                <template #icon>
                    <PencilSquare/>
                </template>
                Edit profile
            </DropdownItem>

            <DropdownItem :href="route('mixpost.logout')" linkAs="button" linkMethod="post">
                <template #icon>
                    <ArrowRightOnRectangleIcon/>
                </template>
                Sign Out
            </DropdownItem>
        </template>
    </Dropdown>
</template>
