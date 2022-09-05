<script setup>
import {computed, ref, watch} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";
import {nanoid} from 'nanoid'
import MixpostMediaFile from "@/Components/Media/MediaFile.vue";
import MixpostMediaSelectable from "@/Components/Media/MediaSelectable.vue";
import PhotoIcon from "@/icons/Photo.vue"

const props = defineProps({
    maxSelection: {
        type: Number,
        default: 1,
    },
    combinesMimeTypes: {
        type: String,
        default: '',
    }
})

const emit = defineEmits(['updateSelect'])

const mimeTypes = usePage().props.value.mixpost.mime_types;

const input = ref(null);

const dragEnter = ref(false);

const onDrop = (e) => {
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

const pending = ref([]);
const completed = ref([]);
const active = ref({});

watch(active, () => {
    processJob();
});

const processJob = () => {
    if (active.value.handler) {
        active.value.handler();
    }
}

const dispatch = (files) => {
    files.forEach((file) => {
        addJob({
            id: nanoid(),
            handler: async () => {
                const media = await uploadFile(file);
                startNextJob(media);
            }
        })
    });
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
        toggleSelect(media);
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
    return completed.value.reverse();
});

const selected = ref([]);

const toggleSelect = (media) => {
    const index = selected.value.findIndex(item => item.id === media.id);

    if (index < 0) {
        selected.value.push(media);
    }

    if (index >= 0) {
        selected.value.splice(index, 1);
    }
}

watch(selected.value, () => {
    emit('updateSelect', selected.value);
});
</script>
<template>
    <div @dragenter.prevent="dragEnter = true"
         @drop.prevent="onDrop"
         @dragover.prevent
         :class="{'border-black bg-white': !dragEnter, 'border-cyan-500 bg-cyan-50': dragEnter}"
         class="w-full flex items-center justify-center rounded-lg p-10 border border-dashed transition-colors ease-in-out duration-200">
        <div class="relative flex flex-col justify-center">
            <div v-if="dragEnter"
                 @dragleave.prevent="dragEnter = false"
                 @dragover.prevent
                 class="w-full h-full absolute"></div>
            <PhotoIcon :class="{'text-stone-700': !dragEnter, 'text-cyan-500': dragEnter}"
                       class="!w-16 !h-16 mx-auto mb-2 transition-colors ease-in-out duration-200"/>
            <div class="text-center mb-1">Drag & drop files here, or
                <label for="browse"
                       class="text-indigo-500 hover:text-indigo-700 active:text-indigo-700 focus:outline-none focus:text-indigo-700 transition-colors ease-in-out duration-200">
                    Browse
                </label>
            </div>
            <div class="text-sm text-gray-400 text-center">{{ mimeTypes.join(', ') }}</div>
        </div>
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

    <div class="mt-6 grid grid-cols-2 sm:grid-cols-3">
        <template v-for="job in completedJobs" :key="job.media.id">
            <MixpostMediaSelectable :active="true" @click="toggleSelect(job.media)">
                <MixpostMediaFile :media="job.media"/>
            </MixpostMediaSelectable>
        </template>
    </div>
</template>
