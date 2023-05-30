<script setup>
import {Link} from '@inertiajs/vue3';
import Logo from "@/Components/DataDisplay/Logo.vue"
import MenuItem from "@/Components/Sidebar/MenuItem.vue"
import MenuDelimiter from "@/Components/Sidebar/MenuDelimiter.vue"
import MenuGroupHeader from "@/Components/Sidebar/MenuGroupHeader.vue"
import MenuGroupBody from "@/Components/Sidebar/MenuGroupBody.vue"
import DarkButtonLink from "@/Components/Button/DarkButtonLink.vue"
import PlusIcon from "@/Icons/Plus.vue"
import GridIcon from "@/Icons/Grid.vue"
import CalendarIcon from "@/Icons/Calendar.vue"
import PhotoIcon from "@/Icons/Photo.vue"
import PackageIcon from "@/Icons/Package.vue"
import CogIcon from "@/Icons/Cog.vue"
import ServerStackIcon from "@/Icons/ServerStack.vue"
import UserMenu from "../Navigation/UserMenu.vue";
</script>
<template>
    <div class="w-full h-full flex flex-col py-2xl bg-white border-r border-gray-200">
        <div class="relative mb-12 px-xl">
            <Link :href="route('mixpost.dashboard')">
                <Logo class="h-12"/>
            </Link>
        </div>

        <div class="flex px-xl">
            <DarkButtonLink :href="route('mixpost.posts.create')" class="w-full">
                <PlusIcon class="mr-xs"/>
                Create post
            </DarkButtonLink>
        </div>

        <div class="flex flex-col space-y-lg overflow-y-auto px-xl mt-2xl h-full">
            <MenuGroupBody>
                <MenuItem :url="route('mixpost.dashboard')" :active="$page.component === 'Dashboard'">
                    <template #icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                    </template>
                    Dashboard
                </MenuItem>
            </MenuGroupBody>
            <MenuDelimiter/>
            <MenuGroupHeader :create-url="route('mixpost.posts.create')" class="mt-lg">
                Content
                <template #icon>
                    <PlusIcon/>
                </template>
            </MenuGroupHeader>
            <MenuGroupBody>
                <MenuItem :url="route('mixpost.posts.index')"
                          :active="$page.component === 'Posts/Index'">
                    <template #icon>
                        <GridIcon/>
                    </template>
                    Posts
                </MenuItem>
                <MenuItem :url="route('mixpost.calendar')" :active="$page.component === 'Calendar'">
                    <template #icon>
                        <CalendarIcon/>
                    </template>
                    Calendar
                </MenuItem>
                <MenuItem :url="route('mixpost.media.index')" :active="$page.component === 'Media'">
                    <template #icon>
                        <PhotoIcon/>
                    </template>
                    Media Library
                </MenuItem>
            </MenuGroupBody>
            <MenuDelimiter/>
            <MenuGroupHeader :create-url="route('mixpost.posts.create')">
                Configuration
            </MenuGroupHeader>
            <MenuGroupBody>
                <MenuItem :url="route('mixpost.accounts.index')" :active="$page.component === 'Accounts/Accounts'">
                    <template #icon>
                        <PackageIcon/>
                    </template>
                    Accounts
                </MenuItem>
                <MenuItem :url="route('mixpost.settings.index')" :active="$page.component === 'Settings'">
                    <template #icon>
                        <CogIcon/>
                    </template>
                    Settings
                </MenuItem>
                <MenuItem :url="route('mixpost.services.index')" :active="$page.component === 'Services'">
                    <template #icon>
                        <ServerStackIcon/>
                    </template>
                    Services
                </MenuItem>
            </MenuGroupBody>
        </div>

        <div class="px-xl pt-xl mb-xl">
            <UserMenu/>
        </div>

        <div class="absolute bottom-0 mb-xs w-full">
            <MenuDelimiter/>
            <div class="flex flex-col items-start  px-xl mt-xs">
                <div class="text-sm text-gray-500">Lite version: {{ $page.props.mixpost.version }}</div>
                <a href="https://mixpost.app/pricing"
                   class="text-green-500  hover:text-green-400 transition-colors ease-in-out duration-200 text-sm">
                    Upgrade to Mixpost Pro
                </a>
            </div>
        </div>
    </div>
</template>
