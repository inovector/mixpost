import NProgress from 'nprogress'
import {computed, nextTick, ref, watch} from "vue";
import {debounce} from "lodash";
import useNotifications from "@/Composables/useNotifications";

const useMedia = (routeName = 'mixpost.media.fetchUploads', routeParams = {}) => {
    const {notify} = useNotifications();

    const activeTab = ref('uploads');

    const tabs = computed(() => {
        return {
            'uploads': 'Upload',
            'stock': 'Stock Photos',
            'gifs': 'GIFs'
        };
    })

    const isLoaded = ref(false);
    const isDownloading = ref(false);
    const isDeleting = ref(false);
    const page = ref(1);
    const items = ref([]);
    const endlessPagination = ref(null);
    const keyword = ref('');

    const selected = ref([]);
    const toggleSelect = (media) => {
        const index = selected.value.findIndex(item => item.id === media.id);

        if (index < 0 && !media.hasOwnProperty('error')) {
            selected.value.push(media);
        }

        if (index >= 0) {
            selected.value.splice(index, 1);
        }
    }

    const deselectAll = () => {
        selected.value = [];
    }

    const isSelected = (media) => {
        const index = selected.value.findIndex(item => item.id === media.id);

        return index !== -1;
    }

    const fetchItems = (appendResult = true) => {
        if (!page.value) {
            return;
        }

        NProgress.start();

        axios.get(route(routeName, routeParams), {
            params: {
                page: page.value,
                keyword: keyword.value
            }
        }).then(function (response) {
            const nextLink = response.data.links.next;

            if (nextLink) {
                page.value = response.data.links.next.split('?page=')[1];
            }

            if (!nextLink) {
                page.value = 0;
            }

            if (!appendResult) {
                items.value = response.data.data;
            }

            if (appendResult) {
                items.value = [...items.value, ...response.data.data];
            }
        }).catch(() => {
            notify('error', 'Error retrieving media. Try again!');
        }).finally(() => {
            NProgress.done();
            isLoaded.value = true;
        });
    }

    const downloadExternal = (items, callback) => {
        isDownloading.value = true;
        NProgress.start();

        axios.post(route('mixpost.media.download', routeParams), {
            items,
            from: activeTab.value,
        }).then((response) => {
            callback(response);
        }).catch(() => {
            notify('error', 'Error downloading media. Try again!');
        }).finally(() => {
            isDownloading.value = false;
            NProgress.done();
            NProgress.remove();
        })
    }

    const removeItems = (ids) => {
        items.value = items.value.filter((item) => !ids.includes(item.id));
    }

    const deletePermanently = (items, callback) => {
        isDeleting.value = true;
        NProgress.start();

        axios.delete(route('mixpost.media.delete', routeParams), {
            data: {
                items
            }
        }).then(() => {
            callback();
        }).catch(() => {
            notify('error', 'Error deleting media. Try again!');
        }).finally(() => {
            isDeleting.value = false;
            NProgress.done();
            NProgress.remove();
        })
    }

    const createObserver = () => {
        const observer = new IntersectionObserver((entries) => {
            const isIntersecting = entries[0].isIntersecting;

            if (isIntersecting) {
                fetchItems();
            }
        });

        nextTick(() => {
            observer.observe(endlessPagination.value);
        });
    }

    watch(keyword, debounce(() => {
        page.value = 1;
        fetchItems(false);
    }, 300));

    return {
        activeTab,
        tabs,
        isLoaded,
        isDownloading,
        isDeleting,
        keyword,
        page,
        items,
        endlessPagination,
        selected,
        downloadExternal,
        deletePermanently,
        removeItems,
        createObserver,
        toggleSelect,
        deselectAll,
        isSelected
    }
}

export default useMedia;
