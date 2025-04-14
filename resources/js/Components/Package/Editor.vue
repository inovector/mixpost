<script setup>
import {ref, onMounted, onUnmounted, useAttrs, watch} from "vue";
import {useEditor, EditorContent} from '@tiptap/vue-3'
import useEditorHelper from "@/Composables/useEditor";
import emitter from "@/Services/emitter";
import History from '@tiptap/extension-history'
import Placeholder from '@tiptap/extension-placeholder'
import Typography from '@tiptap/extension-typography'
import StripLinksOnPaste from "@/Extensions/TipTap/StripLinksOnPaste"
import Hashtag from "@/Extensions/TipTap/Hashtag"
import UserTag from "@/Extensions/TipTap/UserTag"
import ClipboardTextParser from "../../Extensions/ProseMirror/ClipboardTextParser";

const attrs = useAttrs();

const props = defineProps({
    value: {
        required: true,
    },
    editable: {
        type: Boolean,
        default: true,
    },
    placeholder: {
        type: String,
        default: 'Start writing...'
    }
});

const emit = defineEmits(['update']);

const focused = ref(false);

const {defaultExtensions} = useEditorHelper();

const editor = useEditor({
    editable: props.editable,
    content: props.value,
    extensions: [...defaultExtensions, ...[
        History,
        Placeholder.configure({
            placeholder: props.placeholder,
        }),
        Typography.configure({
            openDoubleQuote: false,
            closeDoubleQuote: false,
            openSingleQuote: false,
            closeSingleQuote: false
        }),
        StripLinksOnPaste,
        Hashtag,
        UserTag
    ]],
    editorProps: {
        attributes: {
            class: 'focus:outline-hidden min-h-[150px]',
        },
        clipboardTextParser: ClipboardTextParser,
    },
    onUpdate: () => {
        emit('update', editor.value.getHTML());
    },
    onFocus: () => {
        focused.value = true;
    },
    onBlur: () => {
        focused.value = false;
    }
});

const isEditor = (id) => {
    return attrs.hasOwnProperty('id') && id === attrs.id;
}

onMounted(() => {
    emitter.on('insertEmoji', e => {
        if (isEditor(e.editorId)) {
            editor.value.commands.insertContent(e.emoji.native);
        }
    });

    emitter.on('insertContent', e => {
        if (isEditor(e.editorId)) {
            editor.value.commands.insertContent(e.text);
        }
    });

    emitter.on('focusEditor', e => {
        if (isEditor(e.editorId)) {
            editor.value.commands.focus();
        }
    });
});

onUnmounted(() => {
    editor.value.destroy();
    emitter.off('insertEmoji');
    emitter.off('insertContent');
    emitter.off('focusEditor');
});

watch(() => props.value, (value) => {
    if (value !== editor.value.getHTML()) {
        editor.value.commands.setContent(value);
    }
})
</script>
<template>
    <div
        :class="{'border-indigo-200 ring-3 ring-indigo-200/50': focused}"
        class="border border-gray-200 rounded-md p-md pb-xs text-base transition-colors ease-in-out duration-200">
        <editor-content :editor="editor"/>
        <slot/>
    </div>
</template>
