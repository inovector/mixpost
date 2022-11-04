import {computed, ref} from "vue";

const useSelectable = () => {
    const pageRecords = ref([])
    const selectedRecords = ref([])

    const toggleSelectRecordsOnPage = computed({
        get() {
            return areRecordsSelected(pageRecords.value)
        },
        set() {
            const keys = pageRecords.value

            if (areRecordsSelected(keys)) {
                deselectRecords(keys)

                return
            }

            selectRecords(keys)
        }
    })

    const putPageRecords = (keys) => {
        pageRecords.value = keys
    }

    const selectRecords = (keys) => {
        for (const key of keys) {
            if (isRecordSelected(key)) {
                continue
            }

            selectedRecords.value.push(key)
        }
    }

    const deselectRecord = (key) => {
        let index = selectedRecords.value.indexOf(key)

        if (index !== -1) {
            selectedRecords.value.splice(index, 1)
        }
    }

    const deselectRecords = (keys) => {
        for (const key of keys) {
            deselectRecord(key);
        }
    }

    const deselectAllRecords = () => {
        selectedRecords.value = []
    }

    const isRecordSelected = (key) => {
        return selectedRecords.value.includes(key)
    }

    const areRecordsSelected = (keys) => {
        if(!keys.length) {
            return false;
        }

        return keys.every(key => isRecordSelected(key))
    }

    return {
        selectedRecords,
        toggleSelectRecordsOnPage,
        putPageRecords,
        deselectRecord,
        deselectAllRecords,
    }
}

export default useSelectable;
