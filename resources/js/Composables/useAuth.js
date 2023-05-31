import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";

const useAuth = () => {
    const user = computed(() => {
        return usePage().props.auth.user;
    })

    return {
        user
    }
};

export default useAuth;
