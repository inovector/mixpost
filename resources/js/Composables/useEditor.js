import Document from '@tiptap/extension-document'
import Div from '@/Extensions/TipTap/Div'
import Text from '@tiptap/extension-text'
import Link from '@tiptap/extension-link'
import emitter from "@/Services/emitter";

const useEditor = () => {
    const defaultExtensions = [
        Document,
        Div,
        Text,
        Link.configure({
            openOnClick: false,
            linkOnPaste: false,
        })
    ]

    const insertEmoji = ({editorId, emoji}) => {
        if (emoji.hasOwnProperty('native')) { // We're making sure this is a real emoji
            emitter.emit('insertEmoji', {editorId, emoji});
        }
    }

    const insertContent = ({editorId, text}) => {
        emitter.emit('insertContent', {editorId, text});
    }

    const focusEditor = ({editorId}) => {
        emitter.emit('focusEditor', {editorId});
    }

    const isDocEmpty = (text) => {
        if (text === '<div></div>') {
            return true;
        }

        return text === '';
    }

    const getTextFromHtmlString = (htmlString) => {
        const tempElement = document.createElement("div");
        tempElement.innerHTML = htmlString;

        const innerHTML = tempElement.innerHTML;

        tempElement.remove();

        // Replace empty divs with a placeholder newline character
        let result = innerHTML.replace(/<div><\/div>/g, '\n');

        // Replace start div tags with newline
        result = result.replace(/<div>/g, '\n');

        // Remove all remaining HTML tags (closing div tags in this case)
        result = result.replace(/<\/div>/g, '');

        // Remove all remaining HTML tags
        result = result.replace(/<\/?[^>]+(>|$)/g, "");
        // Replace a tags with their inner text
        // result = result.replace(/<a\s+[^>]*>(.*?)<\/a>/gi, '$1');

        // Remove first newline character if the body starts with a div tag
        // This is a workaround for the fact that the first character is a
        // new line when the body starts with a div tag.
        if (innerHTML.startsWith('<div>')) {
            result = result.substring(1);
        }

        return result;
    }

    return {
        defaultExtensions,
        insertEmoji,
        insertContent,
        focusEditor,
        isDocEmpty,
        getTextFromHtmlString,
    }
}

export default useEditor;
