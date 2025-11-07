import {computed} from "vue";

const useProviderClassesColor = (provider) => {
    const textClasses = computed(() => {
        return {
            'twitter': 'text-twitter',
            'facebook': 'text-facebook',
            'facebook_page': 'text-facebook',
            'facebook_group': 'text-facebook',
            'mastodon': 'text-mastodon',
            'pixelfed': 'text-pixelfed'
        }[provider];
    });

    const borderClasses = computed(() => {
        return {
            'twitter': 'border-twitter',
            'facebook': 'border-facebook',
            'facebook_page': 'border-facebook',
            'facebook_group': 'border-facebook',
            'mastodon': 'border-mastodon',
            'pixelfed': 'border-pixelfed'
        }[provider];
    });

    const activeBgClasses = computed(() => {
        return {
            'twitter': 'bg-twitter',
            'facebook': 'bg-facebook',
            'facebook_page': 'bg-facebook',
            'facebook_group': 'bg-facebook',
            'mastodon': 'bg-mastodon',
            'pixelfed': 'bg-pixelfed'
        }[provider];
    });

    return {
        textClasses,
        borderClasses,
        activeBgClasses
    }
}

export default useProviderClassesColor;
