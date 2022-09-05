<script setup>
import Logo from "@/Components/Logo.vue"
import MixpostMenuItem from "@/Components/MenuItem.vue"
import MixpostMenuDelimiter from "@/Components/MenuDelimiter.vue"
import MixpostMenuGroupHeader from "@/Components/MenuGroupHeader.vue"
import MixpostMenuGroupBody from "@/Components/MenuGroupBody.vue"
import MixpostDarkButtonLink from "@/Components/DarkButtonLink.vue"
import PlusIcon from "@/Icons/Plus.vue"
import GridIcon from "@/Icons/Grid.vue"
import CalendarIcon from "@/Icons/Calendar.vue"
import PhotoIcon from "@/Icons/Photo.vue"
import PackageIcon from "@/Icons/Package.vue"
import CogIcon from "@/Icons/Cog.vue"
import {Link} from '@inertiajs/inertia-vue3';
</script>
<template>
    <div class="w-full h-full flex flex-col py-10 bg-white border-r border-gray-200">
        <div class="relative mb-12 px-8">
            <Link :href="route('mixpost.dashboard')">
                <Logo class="h-12"/>
            </Link>
        </div>

        <div class="flex px-8">
            <MixpostDarkButtonLink :href="route('mixpost.posts.create')" class="w-full"><PlusIcon class="mr-2"/> Create post</MixpostDarkButtonLink>
        </div>

        <div class="flex flex-col space-y-6 overflow-y-auto px-8 mt-10">
            <MixpostMenuGroupBody>
                <MixpostMenuItem :url="route('mixpost.dashboard')" :active="$page.component === 'Dashboard'">
                    <template #icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                    </template>
                    Dashboard
                </MixpostMenuItem>
            </MixpostMenuGroupBody>
            <MixpostMenuDelimiter/>
            <MixpostMenuGroupHeader :create-url="route('mixpost.posts.create')" class="mt-6">
                Content
                <template #icon>
                    <PlusIcon/>
                </template>
            </MixpostMenuGroupHeader>
            <MixpostMenuGroupBody>
                <MixpostMenuItem :url="route('mixpost.posts.index')"
                          :active="route().current('mixpost.posts.index')">
                    <template #icon>
                        <GridIcon/>
                    </template>
                    Posts
                </MixpostMenuItem>
                <MixpostMenuItem :url="route('mixpost.schedule')" :active="route().current('mixpost.schedule')">
                    <template #icon>
                        <CalendarIcon/>
                    </template>
                    Schedule
                </MixpostMenuItem>
                <MixpostMenuItem :url="route('mixpost.media.index')" :active="route().current('mixpost.media.index')">
                    <template #icon>
                        <PhotoIcon/>
                    </template>
                    Media Library
                </MixpostMenuItem>
            </MixpostMenuGroupBody>
            <MixpostMenuDelimiter/>
            <MixpostMenuGroupHeader :create-url="route('mixpost.posts.create')">
                Configuration
            </MixpostMenuGroupHeader>
            <MixpostMenuGroupBody>
                <MixpostMenuItem :url="route('mixpost.accounts.index')" :active="route().current('mixpost.accounts.index')">
                    <template #icon>
                        <PackageIcon/>
                    </template>
                    Accounts
                </MixpostMenuItem>
                <MixpostMenuItem :url="route('mixpost.settings')" :active="route().current('mixpost.settings')">
                    <template #icon>
                        <CogIcon/>
                    </template>
                    Settings
                </MixpostMenuItem>
            </MixpostMenuGroupBody>
        </div>
    </div>
</template>
