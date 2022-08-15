<script setup>
import {watch} from "vue";
import {useEditor, EditorContent} from "@tiptap/vue-3";
import useEditorHelper from "@/Composables/useEditor";

const props = defineProps({
    value: {
        required: true
    }
});

const {defaultExtensions} = useEditorHelper();

const editor = useEditor({
    editable: true,
    content: props.value,
    extensions: defaultExtensions,
});

watch(() => props.value, () => {
   editor.value.commands.setContent(props.value);
});
</script>
<template>
    <editor-content :editor="editor"/>
</template>
