import {computed} from "vue";

const useButtonSize = (size) => {
    const sizeClass = computed(() => {
        return {
            'xs': 'p-1',
            'sm': 'p-2',
            'md': 'px-4 py-2',
            'lg': 'px-4 py-3',
        }[size];
    });

    return {
        sizeClass,
    }
}

export default useButtonSize;
