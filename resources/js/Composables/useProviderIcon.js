import {computed} from "vue";
import TwitterIcon from "@/Icons/Twitter.vue";
import FacebookIcon from "@/Icons/Facebook.vue";


const providerIcon = (provider = null) => {
    const providers = {
        'twitter': TwitterIcon,
        'facebook': FacebookIcon,
    };

    const providerIconComponent = computed(() => {
        return providers[provider];
    });

    const providerIconComponentFnc = (_provider = null)=> {
        return providers[_provider ? _provider : provider];
    }

    return {
        providerIconComponent,
        providerIconComponentFnc
    }
}

export default providerIcon;
