<script setup>
import {watch} from "vue";
import {useEditor, EditorContent} from "@tiptap/vue-3";
import useEditorHelper from "@/Composables/useEditor";
import Hashtag from "@/Extensions/TipTap/Hashtag"
import UserTag from "@/Extensions/TipTap/UserTag"

const props = defineProps({
    value: {
        required: true
    }
});

const {defaultExtensions} = useEditorHelper();

const editor = useEditor({
    editable: false,
    content: props.value,
    extensions: [...defaultExtensions, ...[
        Hashtag,
        UserTag
    ]],
});

watch(() => props.value, () => {
   editor.value.commands.setContent(props.value);
});
</script>
<template>
    <editor-content :editor="editor"/>
</template>
