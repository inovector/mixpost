<script setup>
import {ref, onMounted, onUnmounted, useAttrs, watch} from "vue";
import {useEditor, EditorContent} from '@tiptap/vue-3'
import emitter from "@/Services/emitter";
import Document from '@tiptap/extension-document'
import Paragraph from '@tiptap/extension-paragraph'
import Text from '@tiptap/extension-text'
import History from '@tiptap/extension-history'
import Placeholder from '@tiptap/extension-placeholder'
import Typography from '@tiptap/extension-typography'
import CharacterCount from '@tiptap/extension-character-count'

const attrs = useAttrs();

const props = defineProps({
    modelValue: {
        required: true,
    },
    placeholder: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue']);

const el = ref();
const focused = ref(false);
const content = ref('');

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        Document,
        Paragraph,
        Text,
        History,
        Placeholder.configure({
            placeholder: 'Start writing your post...',
        }),
        Typography.configure({
            openDoubleQuote: false,
            closeDoubleQuote: false,
            openSingleQuote: false,
            closeSingleQuote: false
        }),
        CharacterCount
    ],
    editorProps: {
        attributes: {
            class: 'focus:outline-none min-h-[150px]',
        },
    },
    onUpdate: () => {
        emit('update:modelValue', editor.value.getHTML())
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

watch(() => props.modelValue, () => {
    const characters = editor.value.storage.characterCount.characters();
    console.log(characters)
    // Twitter, No more than 80 characters
});

onMounted(() => {
    emitter.on('insertEmoji', e => {
        if (isEditor(e.editorId)) {
            editor.value.commands.insertContent(e.emoji.native);
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
    emitter.off('focusEditor');
});
</script>
<template>
    <div
        :class="{'border-indigo-200 ring ring-indigo-200 ring-opacity-50': focused}"
        class="border border-gray-200 rounded-md p-5 pb-2 text-base transition-colors ease-in-out duration-200">
        <editor-content :editor="editor"/>
        <slot/>
    </div>
</template>
<style>
.ProseMirror p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    float: left;
    color: theme('colors.gray.400');
    pointer-events: none;
    height: 0;
}
</style>
