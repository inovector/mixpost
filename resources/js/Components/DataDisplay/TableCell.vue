<script setup>
import {computed} from "vue";

const props = defineProps({
    component: {
        type: String,
        default: 'td'
    },
    scope: {
        type: String,
    },
    class: {
        type: String,
    },
    align: {
        type: String,
        default: 'left'
    },
    clickable: {
        type: Boolean,
        default: false,
    }
})

defineEmits(['click'])

const commonClass = 'px-lg py-sm';

const alignClass = computed(() => {
    return {
        'left': 'text-left',
        'right': 'text-right',
    }[props.align];
});
</script>
<template>
    <th v-if="component === 'th'" :scope="scope" :class="[props.class, alignClass, commonClass]" class="font-medium">
        <slot/>
    </th>
    <td v-if="component === 'td'" :class="[props.class, alignClass, commonClass]"
        @click="()=> {clickable ? $emit('click') : undefined}" :role="clickable ? 'button' : 'cell'">
        <slot/>
    </td>
</template>
