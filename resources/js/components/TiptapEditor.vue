<template>
    <div v-if="editor" class="mb-3 form-group-translation">
        <div>
            <div class="control-group">
                <div class="button-group">
                    <button
                        type="button"
                        @click="editor.chain().focus().toggleBold().run()"
                        :disabled="!editor.can().chain().focus().toggleBold().run()"
                        :class="{ 'is-active': editor.isActive('bold') }"
                    >
                        Bold
                    </button>
                    <button
                        type="button"
                        @click="editor.chain().focus().toggleItalic().run()"
                        :disabled="!editor.can().chain().focus().toggleItalic().run()"
                        :class="{ 'is-active': editor.isActive('italic') }"
                    >
                        Italic
                    </button>
                    <button
                        type="button"
                        @click="editor.chain().focus().toggleStrike().run()"
                        :disabled="!editor.can().chain().focus().toggleStrike().run()"
                        :class="{ 'is-active': editor.isActive('strike') }"
                    >
                        Strike
                    </button>
                    <button
                        type="button"
                        @click="editor.chain().focus().toggleCode().run()"
                        :disabled="!editor.can().chain().focus().toggleCode().run()"
                        :class="{ 'is-active': editor.isActive('code') }"
                    >
                        Code
                    </button>
                    <button type="button" @click="editor.chain().focus().unsetAllMarks().run()">Clear marks</button>
                    <button type="button" @click="editor.chain().focus().clearNodes().run()">Clear nodes</button>
                    <button type="button" @click="editor.chain().focus().setParagraph().run()" :class="{ 'is-active': editor.isActive('paragraph') }">Paragraph</button>
                    <button type="button" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }">H1</button>
                    <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }">H2</button>
                    <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }">H3</button>
                    <button type="button" @click="editor.chain().focus().toggleHeading({ level: 4 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 4 }) }">H4</button>
                    <button type="button" @click="editor.chain().focus().toggleHeading({ level: 5 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 5 }) }">H5</button>
                    <button type="button" @click="editor.chain().focus().toggleHeading({ level: 6 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 6 }) }">H6</button>
                    <button type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'is-active': editor.isActive('bulletList') }">Bullet list</button>
                    <button type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'is-active': editor.isActive('orderedList') }">Ordered list</button>
                    <button type="button" @click="editor.chain().focus().toggleCodeBlock().run()" :class="{ 'is-active': editor.isActive('codeBlock') }">Code block</button>
                    <button type="button" @click="editor.chain().focus().toggleBlockquote().run()" :class="{ 'is-active': editor.isActive('blockquote') }">Blockquote</button>
                    <button type="button" @click="editor.chain().focus().setHorizontalRule().run()">Horizontal rule</button>
                    <button type="button" @click="editor.chain().focus().setHardBreak().run()">Hard break</button>
                    <button type="button" @click="editor.chain().focus().undo().run()" :disabled="!editor.can().chain().focus().undo().run()">Undo</button>
                    <button type="button" @click="editor.chain().focus().redo().run()" :disabled="!editor.can().chain().focus().redo().run()">Redo</button>
                    <button type="button" @click="editor.chain().focus().setColor('#958DF1').run()" :class="{ 'is-active': editor.isActive('textStyle', { color: '#958DF1' }) }">Purple</button>
                </div>
            </div>
            <editor-content :editor="editor" :data-language="locale" />
        </div>
    </div>
</template>

<script setup>
import { Color } from '@tiptap/extension-color';
import Image from '@tiptap/extension-image';
import ListItem from '@tiptap/extension-list-item';
import Table from '@tiptap/extension-table';
import TableCell from '@tiptap/extension-table-cell';
import TableHeader from '@tiptap/extension-table-header';
import TableRow from '@tiptap/extension-table-row';
import TextAlign from '@tiptap/extension-text-align';
import TextStyle from '@tiptap/extension-text-style';
import Underline from '@tiptap/extension-underline';
import StarterKit from '@tiptap/starter-kit';
import { Editor, EditorContent } from '@tiptap/vue-3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    locale: {
        type: String,
    },
    initContent: {
        type: String,
        default: '',
    },
});

const editor = ref(null);
const content = ref(props.initContent);

editor.value = new Editor({
    editorProps: {
        attributes: {
            class: 'form-control mb-3',
            id: 'tiptap-input',
        },
    },
    extensions: [
        StarterKit,
        Color.configure({ types: [TextStyle.name, ListItem.name] }),
        TextStyle.configure({ types: [ListItem.name] }),
        Underline,
        Image.configure({
            inline: true,
            allowBase64: true,
        }),
        Table.configure({
            resizable: true,
        }),
        TableRow,
        TableHeader,
        TableCell,
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
    ],
    content: content.value,
});
</script>

<style lang="scss">
/* Basic editor styles */
.tiptap {
    :first-child {
        margin-top: 0;
    }

    /* List styles */
    ul,
    ol {
        padding: 0 1rem;
        margin: 1.25rem 1rem 1.25rem 0.4rem;

        li p {
            margin-top: 0.25em;
            margin-bottom: 0.25em;
        }
    }

    /* Heading styles */
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        line-height: 1.1;
        margin-top: 2.5rem;
        text-wrap: pretty;
    }

    h1,
    h2 {
        margin-top: 3.5rem;
        margin-bottom: 1.5rem;
    }

    h1 {
        font-size: 1.4rem;
    }

    h2 {
        font-size: 1.2rem;
    }

    h3 {
        font-size: 1.1rem;
    }

    h4,
    h5,
    h6 {
        font-size: 1rem;
    }

    /* Code and preformatted text styles */
    code {
        background-color: var(--purple-light);
        border-radius: 0.4rem;
        color: var(--black);
        font-size: 0.85rem;
        padding: 0.25em 0.3em;
    }

    pre {
        background: var(--black);
        border-radius: 0.5rem;
        color: var(--white);
        font-family: 'JetBrainsMono', monospace;
        margin: 1.5rem 0;
        padding: 0.75rem 1rem;

        code {
            background: none;
            color: inherit;
            font-size: 0.8rem;
            padding: 0;
        }
    }

    blockquote {
        border-left: 3px solid var(--gray-3);
        margin: 1.5rem 0;
        padding-left: 1rem;
    }

    hr {
        border: none;
        border-top: 1px solid var(--gray-2);
        margin: 2rem 0;
    }
}
</style>
