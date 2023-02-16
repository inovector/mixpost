import {computed, inject} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";
import {pickBy} from "lodash";

const usePost = () => {
    const post = computed(() => {
        return usePage().props.value.post;
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

    const accountsReachedTextLimit = computed(() => {
        const postContext = inject('postContext')

        const result = pickBy(postContext.textLimit, (item) => item.hit === true);

        if (!Object.keys(result).length) {
            return null;
        }

        return result;
    })

    return {
        postId,
        isInHistory,
        isScheduleProcessing,
        editAllowed,
        accountsReachedTextLimit
    }
}

export default usePost;
