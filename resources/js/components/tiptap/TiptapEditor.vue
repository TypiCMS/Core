<template>
    <div class="mb-3 form-group-translation">
        <div class="tiptap-toolbar" v-if="editor">
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="['tiptap-button', { 'is-active': editor.isActive('heading', { level: 1 }) }]">
                <i class="bi bi-type-h1"></i>
            </button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="['tiptap-button', { 'is-active': editor.isActive('heading', { level: 2 }) }]">
                <i class="bi bi-type-h2"></i>
            </button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="['tiptap-button', { 'is-active': editor.isActive('heading', { level: 3 }) }]">
                <i class="bi bi-type-h3"></i>
            </button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 4 }).run()" :class="['tiptap-button', { 'is-active': editor.isActive('heading', { level: 4 }) }]">
                <i class="bi bi-type-h4"></i>
            </button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 5 }).run()" :class="['tiptap-button', { 'is-active': editor.isActive('heading', { level: 5 }) }]">
                <i class="bi bi-type-h5"></i>
            </button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 6 }).run()" :class="['tiptap-button', { 'is-active': editor.isActive('heading', { level: 6 }) }]">
                <i class="bi bi-type-h6"></i>
            </button>
            <button type="button" @click="editor.chain().focus().setParagraph().run()" :class="['tiptap-button', { 'is-active': editor.isActive('paragraph') }]">
                <i class="bi bi-paragraph"></i>
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" @click="addImage" :class="['tiptap-button']">
                <i class="bi bi-image"></i>
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" @click="addFigure" :class="['tiptap-button']"><i class="bi bi-image"></i></button>
            <button type="button" @click="editor.chain().focus().imageToFigure().run()" :disabled="!editor.can().imageToFigure()" :class="['tiptap-button']">Add caption to image</button>
            <button type="button" @click="editor.chain().focus().figureToImage().run()" :disabled="!editor.can().figureToImage()" :class="['tiptap-button']">Remove caption from image</button>
            <div class="tiptap-separator"></div>
            <button
                type="button"
                @click="editor.chain().focus().toggleBold().run()"
                :disabled="!editor.can().chain().focus().toggleBold().run()"
                :class="['tiptap-button', { 'is-active': editor.isActive('bold') }]"
            >
                <i class="bi bi-type-bold"></i>
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleItalic().run()"
                :disabled="!editor.can().chain().focus().toggleItalic().run()"
                :class="['tiptap-button', { 'is-active': editor.isActive('italic') }]"
            >
                <i class="bi bi-type-italic"></i>
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleStrike().run()"
                :disabled="!editor.can().chain().focus().toggleStrike().run()"
                :class="['tiptap-button', { 'is-active': editor.isActive('strike') }]"
            >
                <i class="bi bi-type-strikethrough"></i>
            </button>
            <button type="button" @click="editor.chain().focus().unsetAllMarks().run()" :class="['tiptap-button']">
                <i class="bi bi-x"></i>
            </button>
            <div class="tiptap-separator"></div>

            <button type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="['tiptap-button', { 'is-active': editor.isActive('bulletList') }]">
                <i class="bi bi-list-ul"></i>
            </button>
            <button type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="['tiptap-button', { 'is-active': editor.isActive('orderedList') }]">
                <i class="bi bi-list-ol"></i>
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" @click="editor.chain().focus().toggleCodeBlock().run()" :class="['tiptap-button', { 'is-active': editor.isActive('codeBlock') }]">
                <i class="bi bi-code-square"></i>
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleCode().run()"
                :disabled="!editor.can().chain().focus().toggleCode().run()"
                :class="['tiptap-button', { 'is-active': editor.isActive('code') }]"
            >
                <i class="bi bi-code"></i>
            </button>
            <div class="tiptap-separator"></div>
            <button
                type="button"
                @click="
                    editor.isActive('subscript') && editor.chain().focus().unsetSubscript().run();
                    editor.chain().focus().toggleSuperscript().run();
                "
                :disabled="!editor.can().chain().focus().toggleSuperscript().run()"
                :class="['tiptap-button', { 'is-active': editor.isActive('superscript') }]"
            >
                <i class="bi bi-superscript"></i>
            </button>
            <button
                type="button"
                @click="
                    editor.isActive('superscript') && editor.chain().focus().unsetSuperscript().run();
                    editor.chain().focus().toggleSubscript().run();
                "
                :disabled="!editor.can().chain().focus().toggleSubscript().run()"
                :class="['tiptap-button', { 'is-active': editor.isActive('subscript') }]"
            >
                <i class="bi bi-subscript"></i>
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" @click="editor.chain().focus().toggleBlockquote().run()" :class="['tiptap-button', { 'is-active': editor.isActive('blockquote') }]">
                <i class="bi bi-quote"></i>
            </button>
            <button type="button" @click="editor.chain().focus().setHorizontalRule().run()" :class="['tiptap-button']">
                <i class="bi bi-dash-lg"></i>
            </button>
            <button type="button" @click="editor.chain().focus().setHardBreak().run()" :class="['tiptap-button']">
                <i class="bi bi-text-wrap"></i>
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" @click="editor.chain().focus().undo().run()" :disabled="!editor.can().chain().focus().undo().run()" :class="['tiptap-button']">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
            <button type="button" @click="editor.chain().focus().redo().run()" :disabled="!editor.can().chain().focus().redo().run()" :class="['tiptap-button']">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
        <editor-content :editor="editor" :data-language="locale" />
        <textarea :name="name" class="d-none" v-if="editor">{{ editor.getHTML() }}</textarea>
    </div>
</template>

<script setup>
import { Color } from '@tiptap/extension-color';
import Image from '@tiptap/extension-image';
import ListItem from '@tiptap/extension-list-item';
import Subscript from '@tiptap/extension-subscript';
import Superscript from '@tiptap/extension-superscript';
import Table from '@tiptap/extension-table';
import TableCell from '@tiptap/extension-table-cell';
import TableHeader from '@tiptap/extension-table-header';
import TableRow from '@tiptap/extension-table-row';
import TextAlign from '@tiptap/extension-text-align';
import TextStyle from '@tiptap/extension-text-style';
import Typography from '@tiptap/extension-typography';
import Underline from '@tiptap/extension-underline';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Figure } from './figure.ts';

const { t } = useI18n();

const props = defineProps({
    name: {
        type: String,
    },
    locale: {
        type: String,
    },
    initContent: {
        type: String,
        default: '',
    },
});

const content = ref(props.initContent);

const CustomImage = Image.extend({
    addAttributes() {
        return {
            src: {
                default: null,
            },
            width: {
                default: null,
            },
            height: {
                default: null,
            },
            alt: {
                default: null,
            },
        };
    },
    // renderHTML({ HTMLAttributes }) {
    //     return ['figure', ['img', HTMLAttributes, 0]];
    // },
});

const editor = useEditor({
    editorProps: {
        attributes: {
            class: 'form-control mb-3',
            id: 'tiptap-input',
        },
    },
    extensions: [
        StarterKit,
        Typography,
        Superscript,
        Subscript,
        Color.configure({ types: [TextStyle.name, ListItem.name] }),
        TextStyle.configure({ types: [ListItem.name] }),
        Underline,
        Figure,
        CustomImage.configure({
            HTMLAttributes: {
                class: 'img-fluid',
            },
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

const addFigure = function () {
    emitter.emit('openFilePicker', {
        modal: true,
        modalIsInFront: true,
        multiple: false,
        open: true,
        overlay: true,
        single: true,
    });
    emitter.on('fileAdded', (image) => {
        if (image.url) {
            editor.value
                .chain()
                .focus()
                .setImage({
                    src: image.url,
                    alt: image.alt_attribute[props.locale] || '',
                    width: image.width || null,
                    height: image.height || null,
                })
                .run();
        }
    });
};

const addImage = function () {
    emitter.emit('openFilePicker', {
        modal: true,
        modalIsInFront: true,
        multiple: false,
        open: true,
        overlay: true,
        single: true,
    });
    emitter.on('fileAdded', (image) => {
        if (image.url) {
            editor.value
                .chain()
                .focus()
                .setImage({
                    src: image.url,
                    alt: image.alt_attribute[props.locale] || '',
                    width: image.width || null,
                    height: image.height || null,
                })
                .run();
        }
    });
};
</script>
