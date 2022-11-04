<script setup>
import {ref, onMounted, onUnmounted, useAttrs, computed, watch} from "vue";
import {useEditor, EditorContent} from '@tiptap/vue-3'
import useEditorHelper from "@/Composables/useEditor";
import emitter from "@/Services/emitter";
import History from '@tiptap/extension-history'
import Placeholder from '@tiptap/extension-placeholder'
import Typography from '@tiptap/extension-typography'

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
        default: ''
    }
});

const emit = defineEmits(['update']);

const el = ref();
const focused = ref(false);

const {defaultExtensions} = useEditorHelper();

const editor = useEditor({
    editable: props.editable,
    content: props.value,
    extensions: [...defaultExtensions, ...[
        History,
        Placeholder.configure({
            placeholder: 'Start writing your post...',
        }),
        Typography.configure({
            openDoubleQuote: false,
            closeDoubleQuote: false,
            openSingleQuote: false,
            closeSingleQuote: false
        })
    ]],
    editorProps: {
        attributes: {
            class: 'focus:outline-none min-h-[150px]',
        },
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

const bodyText = computed(() => {
    return editor.value && !editor.value.isEmpty ? editor.value.view.dom.innerText : '';
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

watch(() => props.value, (value) => {
    if (value !== editor.value.getHTML()) {
        editor.value.commands.setContent(value);
    }
})
</script>
<template>
    <div
        :class="{'border-indigo-200 ring ring-indigo-200 ring-opacity-50': focused}"
        class="border border-gray-200 rounded-md p-5 pb-2 text-base transition-colors ease-in-out duration-200">
        <editor-content :editor="editor"/>
        <slot :body-text="bodyText"/>
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

.ProseMirror a {
    color: theme('colors.blue.500');
}
</style>
