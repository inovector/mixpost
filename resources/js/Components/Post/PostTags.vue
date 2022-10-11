<script setup>
import {computed, ref} from "vue";
import {usePage} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {difference, last, random} from "lodash";
import {decomposeString} from "@/helpers";
import {COLOR_PALLET_LIST} from "@/Constants/ColorPallet";
import Tag from "@/Components/DataDisplay/Tag.vue";
import TagIcon from "@/Icons/Tag.vue"
import DialogModal from "@/Components/Modal/DialogModal.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import Input from "@/Components/Form/Input.vue";
import Preloader from "@/Components/Util/Preloader.vue";

const props = defineProps({
    items: {
        type: Array,
        default: []
    }
})

const emit = defineEmits(['update'])

const showManager = ref(false);
const searchText = ref('');
const isLoading = ref(false);

const tags = computed(() => {
    return usePage().props.value.tags;
})

const availableTags = computed(() => {
    return tags.value.filter((tag) => {
        const search = decomposeString(tag.name).toLocaleLowerCase().includes(searchText.value.toLocaleLowerCase())

        return !props.items.some(item => item.id === tag.id) && search;
    })
})

const select = (tag, $event = null) => {
    if (!$event || ($event && !$event.target.closest('.tag-actions'))) {
        emit('update', [...props.items.slice(0), ...[tag]]);
    }
}

const remove = (tag) => {
    emit('update', props.items.filter(item => item.id !== tag.id));
}

const store = () => {
    if (isLoading.value) {
        return;
    }

    if (!searchText.value) {
        return;
    }

    // Avoid duplicate tags with the same name
    if (availableTags.value.length === 1 && searchText.value && decomposeString(searchText.value) === decomposeString(availableTags.value[0].name)) {
        searchText.value = '';
        select(availableTags.value[0]);
        return;
    }

    const pickRandomColor = () => {
        const colorList = COLOR_PALLET_LIST();

        const nonUsedColors = difference(colorList, tags.value.map(tag => tag.hex_color));

        if (!nonUsedColors.length) {
            return colorList[random(0, colorList.length)]
        }

        return nonUsedColors[random(0, nonUsedColors.length)]
    }

    Inertia.post(route('mixpost.tags.store'), {
        name: searchText.value,
        hex_color: pickRandomColor()
    }, {
        onStart() {
            isLoading.value = true
        },
        onSuccess() {
            searchText.value = '';
            select(last(tags.value));
        },
        onFinish() {
            isLoading.value = false
        }
    })
}
</script>
<template>
    <div>
        <div class="flex items-center">
            <div class="hidden lg:flex items-center space-x-2 mr-2">
                <template v-for="item in items" :key="item.id">
                    <Tag :item="item" @remove="remove(item)"/>
                </template>
            </div>

            <SecondaryButton @click="showManager = true" size="md">
                <TagIcon class="lg:mr-2"/>
                <span class="hidden lg:block">Labels</span>
            </SecondaryButton>
        </div>

        <DialogModal :show="showManager"
                     max-width="sm"
                     :closeable="true"
                     :scrollable-body="true"
                     @close="showManager = false">
            <template #header>
                Labels
            </template>

            <template #body>
                <div class="relative">
                    <Preloader v-if="isLoading" :opacity="50"/>

                    <div class="flex flex-wrap items-center gap-2">
                        <div v-for="item in items" :key="item.id">
                            <Tag :item="item" @remove="remove(item)"/>
                        </div>
                    </div>

                    <div class="mt-4">
                        <Input v-model="searchText"
                               @keyup.enter="store"
                               type="text"
                               autofocus
                               placeholder="Search or Create New"
                               class="w-full"/>
                    </div>

                    <div v-if="availableTags.length" class="mt-2 border border-gray-300 rounded-md">
                        <template v-for="item in availableTags">
                            <div @click="select(item, $event)"
                                 tabindex="0"
                                 role="button"
                                 class="flex items-center justify-between p-2 rounded-t-md last:rounded-t-none last:rounded-b-md border-b last:border-none hover:bg-gray-100 transition-colors ease-in-out duration-200">
                                <Tag :item="item" :removable="false"/>
                            </div>
                        </template>
                    </div>

                    <div v-if="searchText" class="mt-4 text-stone-800 italic">
                        Press Enter to create a new label
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="showManager = false" class="mr-2">Done</SecondaryButton>
            </template>
        </DialogModal>
    </div>
</template>