import {computed} from "vue";

const useProviderClassesColor = (provider) => {
    const textClasses = computed(() => {
        return {
            'twitter': 'text-twitter',
            'facebook': 'text-facebook',
            'facebook_page': 'text-facebook',
            'facebook_group': 'text-facebook',
        }[provider];
    });

    const borderClasses = computed(() => {
        return {
            'twitter': 'border-twitter',
            'facebook': 'border-facebook',
            'facebook_page': 'border-facebook',
            'facebook_group': 'border-facebook',
        }[provider];
    });

    const activeBgClasses = computed(() => {
        return {
            'twitter': 'bg-twitter',
            'facebook': 'bg-facebook',
            'facebook_page': 'bg-facebook',
            'facebook_group': 'bg-facebook',
        }[provider];
    });

    return {
        textClasses,
        borderClasses,
        activeBgClasses
    }
}

export default useProviderClassesColor;
