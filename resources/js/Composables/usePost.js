import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

const usePost = () => {
    const post = computed(() => {
        return usePage().props.value.post;
    });

    const postId = computed(() => {
        return post.value ? post.value.id : null;
    });

    const isReadOnly = computed(() => {
        if (!post.value) {
            return false;
        }

        return ['PUBLISHED', 'PUBLISHING', 'ERROR'].includes(post.value.status)
    })

    return {
        postId,
        isReadOnly: isReadOnly
    }
}

export default usePost;
