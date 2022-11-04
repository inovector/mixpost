<script setup>
import {onMounted, onUnmounted, ref} from "vue";
import iro from "@jaames/iro";
import {COLOR_PALLET_LIST} from "@/Constants/ColorPallet";
import CheckIcon from "@/Icons/Check.vue";
import Input from "@/Components/Form/Input.vue";

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    colors: {
        type: Array,
        default: () => {
            return COLOR_PALLET_LIST();
        }
    }
});

const emit = defineEmits(['update:modelValue']);

const el = ref(null);

let colorPicker = null;

const onColorPickerChange = (val) => {
    emit('update:modelValue', val.hexString);
}

const onChange = (val) => {
    emit('update:modelValue', val)
    colorPicker.color.set(val);
}

onMounted(() => {
    colorPicker = new iro.ColorPicker(el.value, {
        color: props.modelValue,
        width: 200
    });

    colorPicker.on('color:change', onColorPickerChange)
})

onUnmounted(() => {
    colorPicker.off('color:change', onColorPickerChange);
})
</script>
<template>
    <div class="flex items-center justify-between">
        <div>
            <div class="grid grid-cols-4 gap-sm">
                <template v-for="(colorHex, index) in colors" :key="index">
                    <button @click="onChange(colorHex)" role="button" type="button"
                            :style="{backgroundColor: colorHex}"
                            class="flex items-center justify-center w-6 h-6 rounded-md">
                        <CheckIcon v-if="colorHex === modelValue" class="text-white"/>
                    </button>
                </template>
            </div>
            <Input type="text" :value="modelValue" @update:modelValue="onChange"
                   class="mt-4 w-32"/>
        </div>

        <div ref="el"/>
    </div>
</template>
