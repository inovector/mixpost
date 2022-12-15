import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

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

    return {
        postId,
        isInHistory,
        isScheduleProcessing,
        editAllowed
    }
}

export default usePost;
