import {computed} from "vue";
import TwitterIcon from "@/Icons/Twitter.vue";
import FacebookIcon from "@/Icons/Facebook.vue";


const providerIcon = (provider) => {
    const providerIconComponent = computed(() => {
        return {
            'twitter': TwitterIcon,
            'facebook': FacebookIcon,
        }[provider];
    });

    return {
        providerIconComponent
    }
}

export default providerIcon;
