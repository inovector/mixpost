import emitter from "@/Services/emitter";

const useEditors = () => {
    const insertEmoji = ({editorId, emoji}) => {
        if (emoji.hasOwnProperty('native')) { // We're making sure this is a real emoji
            emitter.emit('insertEmoji', {editorId, emoji});
        }
    }

    const focusEditor = ({editorId}) => {
        emitter.emit('focusEditor', {editorId});
    }

    return {
        insertEmoji,
        focusEditor
    }
}

export default useEditors;
