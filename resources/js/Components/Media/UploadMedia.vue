<script setup>
import {computed, ref, watch} from "vue";
import {usePage} from "@inertiajs/vue3";
import {nanoid} from 'nanoid'
import Masonry from "@/Components/Layout/Masonry.vue";
import MediaFile from "@/Components/Media/MediaFile.vue";
import MediaSelectable from "@/Components/Media/MediaSelectable.vue";
import Preloader from "@/Components/Util/Preloader.vue"
import PhotoIcon from "@/Icons/Photo.vue"

const props = defineProps({
    maxSelection: {
        type: Number,
        default: 1,
    },
    combinesMimeTypes: {
        type: String,
        default: '',
    },
    selected: {
        type: Array,
        default: []
    },
    toggleSelect: {
        type: Function
    },
    isSelected: {
        type: Function
    },
    columns: {
        type: Number,
        default: 3,
    }
})

const emit = defineEmits(['mediaSelect'])

const mimeTypes = usePage().props.mixpost.mime_types;

const input = ref(null);

const dragEnter = ref(false);

const onDrop = (e) => {
    if (isLoading.value) {
        return;
    }

    dragEnter.value = false;

    const files = filterFiles(e.dataTransfer.files);

    if (files.length) {
        dispatch(files);
    }
}

const onBrowse = (e) => {
    const files = filterFiles(e.target.files);

    if (files.length) {
        input.value.value = null;
        dispatch(files);
    }
}

const filterFiles = (files) => {
    return Array.from(files).filter((file) => {
        return mimeTypes.includes(file.type);
    });
}

const isLoading = ref(false);
const pending = ref([]);
const completed = ref([]);
const active = ref({});

watch(active, () => {
    processJob();

    isLoading.value = Object.keys(active.value).length > 0;
});

const processJob = () => {
    if (active.value.handler) {
        active.value.handler();
    }
}

const addJob = (job) => {
    pending.value.push(job);

    if (Object.keys(active.value).length === 0) {
        startNextJob();
    }
}

const startNextJob = (media) => {
    if (Object.keys(active.value).length > 0) {
        addCompletedJob(active.value, media);

        if (props.toggleSelect) {
            props.toggleSelect(media);
        }
    }

    if (pending.value.length > 0) {
        setActiveJob(pending.value[0]);
        popCurrentJob();
    } else {
        setActiveJob({});
    }
}

const setActiveJob = (job) => {
    active.value = job;
}

const popCurrentJob = () => {
    pending.value.shift();
}

const addCompletedJob = (job, media) => {
    completed.value.push(Object.assign(job, {media}));
}

const dispatch = (files) => {
    files.forEach((file) => {
        addJob({
            id: nanoid(),
            handler: async () => {
                await uploadFile(file).then((media) => {
                    startNextJob(media);
                }).catch((error) => {
                    startNextJob({
                        name: file.name,
                        error: error.response.data.message
                    });
                });
            }
        })
    });
}

const uploadFile = (file) => {
    const formData = new FormData();
    formData.append("file", file);

    return new Promise((resolve, reject) => {
        axios.post(route('mixpost.media.upload'), formData)
            .then(function (response) {
                resolve(response.data);
            })
            .catch(function (error) {
                reject(error);
            });
    });
}

const completedJobs = computed(() => {
    return completed.value.filter(() => true).reverse();
});

const selected = ref([]);
</script>
<template>
    <div @dragenter.prevent="dragEnter = !isLoading"
         @drop.prevent="onDrop"
         @dragover.prevent
         :class="{'border-gray-700 bg-white': !dragEnter, 'border-cyan-500 bg-cyan-50': dragEnter}"
         class="relative w-full flex items-center justify-center rounded-lg p-10 border-2 border-dashed transition-colors ease-in-out duration-200">
        <div class="relative flex flex-col justify-center">
            <div v-if="dragEnter"
                 @dragleave.prevent="dragEnter = false"
                 @dragover.prevent
                 class="w-full h-full absolute"></div>
            <PhotoIcon :class="{'text-stone-700': !dragEnter, 'text-cyan-500': dragEnter}"
                       class="w-16! h-16! mx-auto mb-xs transition-colors ease-in-out duration-200"/>
            <div class="text-center mb-1">Drag & drop files here, or
                <label for="browse"
                       class="cursor-pointer text-indigo-500 hover:text-indigo-700 active:text-indigo-700 focus:outline-hidden focus:text-indigo-700 transition-colors ease-in-out duration-200">
                    Browse
                </label>
            </div>
            <div class="text-sm text-gray-400 text-center">{{ mimeTypes.join(', ') }}</div>
        </div>
        <Preloader v-if="isLoading" size="xl" class="rounded-lg"/>
    </div>

    <input
        ref="input"
        id="browse"
        type="file"
        :accept="mimeTypes.join(',')"
        multiple="multiple"
        class="hidden"
        @change="onBrowse"
    />

    <div v-if="completedJobs.length" class="mt-lg">
        <Masonry :items="completedJobs" :columns="columns">
            <template #default="{item}">
                <MediaSelectable :active="isSelected(item.media)" @click="toggleSelect(item.media)">
                    <MediaFile :media="item.media"/>
                </MediaSelectable>
            </template>
        </Masonry>
    </div>
</template>
