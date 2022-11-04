import {onMounted, ref} from "vue";

const useMounted = () => {
    const isMounted = ref(false);

    onMounted(() => {
        isMounted.value = true;
    });

    return {
        isMounted,
    }
}

export default useMounted;
