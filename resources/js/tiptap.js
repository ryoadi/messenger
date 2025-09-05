import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'

window.setupEditor = function (content) {
    let editor

    return {
        content: content,

        init(element) {
            editor = new Editor({
                element: element,
                extensions: [StarterKit],
                content: this.content,
                onUpdate: ({ editor }) => {
                    this.content = editor.getHTML()
                },
            })

            this.$watch('content', (content) => {
                // If the new content matches Tiptap's then we just skip.
                if (content === editor.getHTML()) return

                /*
                  Otherwise, it means that an external source
                  is modifying the data on this Alpine component,
                  which could be Livewire itself.
                  In this case, we only need to update Tiptap's
                  content and we're done.
                  For more information on the `setContent()` method, see:
                    https://www.tiptap.dev/api/commands/set-content
                */
                editor.commands.setContent(content, false)
            })
        },
    }
}
