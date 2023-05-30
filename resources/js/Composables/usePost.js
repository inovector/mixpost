import {computed, inject} from "vue";
import {usePage} from "@inertiajs/vue3";
import {filter} from "lodash";

const usePost = () => {
    const post = computed(() => {
        return usePage().props.post;
    });

    const postId = computed(() => {
        return post.value ? post.value.id : null;
    });

    const isInHistory = computed(() => {
        if (!post.value) {
            return false;
        }

        return ['PUBLISHED', 'FAILED'].includes(post.value.status)
    })

    const isScheduleProcessing = computed(() => {
        if (!post.value) {
            return false;
        }

        return post.value.status === 'PUBLISHING';
    })

    const editAllowed = computed(() => {
        return !(isInHistory.value || isScheduleProcessing.value);
    });

    const accountsHitTextLimit = computed(() => {
        const postCtx = inject('postCtx')

        return filter(postCtx.textLimit, {hit: true});
    })

    const accountsHitMediaLimit = computed(() => {
        const postCtx = inject('postCtx')

        return filter(postCtx.mediaLimit, (item) => {
            return item.photos.hit || item.videos.hit || item.gifs.hit || item.mixing.hit;
        });
    })

    return {
        postId,
        isInHistory,
        isScheduleProcessing,
        editAllowed,
        accountsHitTextLimit,
        accountsHitMediaLimit
    }
}

export default usePost;
