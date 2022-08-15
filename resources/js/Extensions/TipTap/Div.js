import { mergeAttributes, Node } from '@tiptap/core'

const Div = Node.create({
    name: 'div',

    priority: 1000,

    addOptions() {
        return {
            HTMLAttributes: {},
        }
    },

    group: 'block',

    content: 'inline*',

    parseHTML() {
        return [
            { tag: 'div' },
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return ['div', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes), 0]
    },

    addCommands() {
        return {
            setParagraph: () => ({ commands }) => {
                return commands.setNode(this.name)
            },
        }
    },

    addKeyboardShortcuts() {
        return {
            'Mod-Alt-0': () => this.editor.commands.setParagraph(),
        }
    },
})

export default Div;
