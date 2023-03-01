import {Extension} from '@tiptap/core'
import {Plugin, PluginKey} from 'prosemirror-state'

const StripLinksOnPaste = Extension.create({
    name: 'StripLinksOnPaste',
    addProseMirrorPlugins() {
        return [
            new Plugin({
                key: new PluginKey('StripLinksOnPaste'),
                props: {
                    transformPastedHTML(html) {
                        return html.replace(/<a[^>]*>/g, '').replace(/<\/a>/g, '');
                    },
                    // Here is the full list: https://prosemirror.net/docs/ref/#view.EditorProps
                },
            }),
        ]
    },
});

export default StripLinksOnPaste;
