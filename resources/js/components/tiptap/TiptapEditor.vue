<template>
    <div class="mb-3 form-group-translation">
        <p v-if="label" class="form-label">{{ label }}</p>
        <div class="tiptap-toolbar" v-if="editor">
            <div class="dropdown">
                <button
                    class="tiptap-button dropdown-toggle text-start d-flex justify-content-between align-items-center"
                    style="width: 100px"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    :disabled="editor.isActive('image')"
                >
                    <span v-if="editor.isActive('heading')" class="">
                        <template v-for="level in [1, 2, 3, 4, 5, 6]" :key="level">
                            <span v-if="editor.isActive('heading', { level })">{{ t('Heading') }} {{ level }}</span>
                        </template>
                    </span>
                    <span v-else>{{ t('Normal') }}</span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <button
                            class="dropdown-item small d-flex gap-1 align-items-center"
                            @click="editor.chain().focus().setParagraph().run()"
                            :class="{ active: editor.isActive('paragraph') }"
                            type="button"
                        >
                            {{ t('Normal') }}
                        </button>
                    </li>
                    <li v-for="level in [1, 2, 3, 4, 5, 6]" :key="level">
                        <button
                            class="dropdown-item small d-flex gap-1 align-items-center"
                            :class="{ active: editor.isActive('heading', { level }) }"
                            @click="editor.chain().focus().toggleHeading({ level }).run()"
                            type="button"
                        >
                            {{ t('Heading') }} {{ level }}
                        </button>
                    </li>
                </ul>
            </div>
            <div class="dropdown">
                <button
                    class="tiptap-button dropdown-toggle text-start d-flex justify-content-between align-items-center"
                    style="width: 100px"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    :disabled="editor.isActive('image')"
                >
                    {{ t('Style') }}
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <p class="dropdown-header text-uppercase fw-light">{{ t('Paragraph Style') }}</p>
                    </li>
                    <li v-for="blockStyle in blockStyles">
                        <button
                            type="button"
                            class="dropdown-item small d-flex gap-1 align-items-center"
                            :class="{ active: editor.isActive(blockStyle.tag, { class: blockStyle.class }) }"
                            @click="
                                editor.isActive(blockStyle.tag, { class: blockStyle.class })
                                    ? editor.commands.updateAttributes(blockStyle.tag, { class: null })
                                    : editor.commands.updateAttributes(blockStyle.tag, { class: blockStyle.class })
                            "
                        >
                            {{ t(blockStyle.label) }}
                        </button>
                    </li>
                    <li v-if="editor.isActive('link')">
                        <p class="dropdown-header text-uppercase fw-light">{{ t('Link Style') }}</p>
                    </li>
                    <li v-for="linkStyle in linkStyles" v-if="editor.isActive('link')">
                        <button
                            type="button"
                            class="dropdown-item small d-flex gap-1 align-items-center"
                            :class="{ active: editor.isActive('link', { class: linkStyle.class }) }"
                            @click="
                                editor.isActive('link', { class: linkStyle.class })
                                    ? editor.commands.updateAttributes('link', { class: null })
                                    : editor.commands.updateAttributes('link', { class: linkStyle.class })
                            "
                        >
                            {{ t(linkStyle.label) }}
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tiptap-separator"></div>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Bold')"
                v-tooltip
                @click="editor.chain().focus().toggleBold().run()"
                :disabled="!editor.can().chain().focus().toggleBold().run()"
                :class="{ 'is-active': editor.isActive('bold') }"
            >
                <bold-icon size="18" stroke-width="1.5" />
            </button>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Italic')"
                v-tooltip
                @click="editor.chain().focus().toggleItalic().run()"
                :disabled="!editor.can().chain().focus().toggleItalic().run()"
                :class="{ 'is-active': editor.isActive('italic') }"
            >
                <italic-icon size="18" stroke-width="1.5" />
            </button>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Strikethrough')"
                v-tooltip
                @click="editor.chain().focus().toggleStrike().run()"
                :disabled="!editor.can().chain().focus().toggleStrike().run()"
                :class="{ 'is-active': editor.isActive('strike') }"
            >
                <strikethrough-icon size="18" stroke-width="1.5" />
            </button>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Superscript')"
                v-tooltip
                @click="
                    editor.isActive('subscript') && editor.chain().focus().unsetSubscript().run();
                    editor.chain().focus().toggleSuperscript().run();
                "
                :disabled="!editor.can().chain().focus().toggleSuperscript().run()"
                :class="{ 'is-active': editor.isActive('superscript') }"
            >
                <superscript-icon size="18" stroke-width="1.5" />
            </button>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Subscript')"
                v-tooltip
                @click="
                    editor.isActive('superscript') && editor.chain().focus().unsetSuperscript().run();
                    editor.chain().focus().toggleSubscript().run();
                "
                :disabled="!editor.can().chain().focus().toggleSubscript().run()"
                :class="{ 'is-active': editor.isActive('subscript') }"
            >
                <subscript-icon size="18" stroke-width="1.5" />
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" class="tiptap-button" :title="t('Remove Format')" v-tooltip @click="editor.chain().focus().unsetAllMarks().run()" :disabled="editor.isActive('image')">
                <remove-formatting-icon size="18" stroke-width="1.5" />
            </button>
            <div class="tiptap-separator"></div>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Insert/Remove Bulleted List')"
                v-tooltip
                @click="editor.chain().focus().toggleBulletList().run()"
                :class="{ 'is-active': editor.isActive('bulletList') }"
                :disabled="editor.isActive('image')"
            >
                <list-icon size="18" stroke-width="1.5" />
            </button>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Insert/Remove Numbered List')"
                v-tooltip
                @click="editor.chain().focus().toggleOrderedList().run()"
                :class="{ 'is-active': editor.isActive('orderedList') }"
                :disabled="editor.isActive('image')"
            >
                <list-ordered-icon size="18" stroke-width="1.5" />
            </button>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Increase Indent')"
                v-tooltip
                @click="editor.chain().focus().sinkListItem('listItem').run()"
                :disabled="!editor.can().sinkListItem('listItem')"
            >
                <indent-increase-icon size="18" stroke-width="1.5" />
            </button>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Decrease Indent')"
                v-tooltip
                @click="editor.chain().focus().liftListItem('listItem').run()"
                :disabled="!editor.can().liftListItem('listItem')"
            >
                <indent-decrease-icon size="18" stroke-width="1.5" />
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" class="tiptap-button" :title="t('Link')" v-tooltip @click="openLinkDialog" :class="{ 'is-active': editor.isActive('link') }" :disabled="editor.isActive('image')">
                <link2-icon size="18" stroke-width="1.5" />
            </button>
            <button type="button" class="tiptap-button" :title="t('Unset Link')" v-tooltip @click="editor.chain().focus().unsetLink().run()" :disabled="editor.isActive('image')">
                <link2-off-icon size="18" stroke-width="1.5" />
            </button>
            <div class="tiptap-separator"></div>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Block Quote')"
                v-tooltip
                @click="editor.chain().focus().toggleBlockquote().run()"
                :class="{ 'is-active': editor.isActive('blockquote') }"
            >
                <message-square-quote-icon size="18" stroke-width="1.5" />
            </button>
            <button type="button" class="tiptap-button" :title="t('Create Div Container')" v-tooltip @click="editor.chain().focus().toggleDiv().run()" :class="{ active: editor.isActive('div') }">
                <text-select-icon size="18" stroke-width="1.5" />
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" class="tiptap-button" @click="editor.chain().focus().setTextAlign('left').run()" :class="{ 'is-active': editor.isActive({ textAlign: 'left' }) }">
                <align-left-icon size="18" stroke-width="1.5" />
            </button>
            <button type="button" class="tiptap-button" @click="editor.chain().focus().setTextAlign('center').run()" :class="{ 'is-active': editor.isActive({ textAlign: 'center' }) }">
                <align-center-icon size="18" stroke-width="1.5" />
            </button>
            <button type="button" class="tiptap-button" @click="editor.chain().focus().setTextAlign('right').run()" :class="{ 'is-active': editor.isActive({ textAlign: 'right' }) }">
                <align-right-icon size="18" stroke-width="1.5" />
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" class="tiptap-button" :title="t('Image')" v-tooltip @click="openImageDialog" :class="{ 'is-active': editor.isActive('image') }">
                <image-icon size="18" stroke-width="1.5" />
            </button>
            <button type="button" class="tiptap-button" :title="t('YouTube Video')" v-tooltip @click="openYoutubeDialog" :class="{ 'is-active': editor.isActive('youtube') }">
                <youtube-icon size="18" stroke-width="1.5" />
            </button>
            <button type="button" class="tiptap-button" :title="t('Embed Iframe')" v-tooltip @click="openIframeDialog">
                <video-icon size="18" stroke-width="1.5" />
            </button>

            <!-- Table-->
            <div class="dropdown">
                <button class="tiptap-button dropdown-toggle text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <sheet-icon size="18" stroke-width="1.5" />
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <button type="button" class="dropdown-item small d-flex gap-1 align-items-center" @click="editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()">
                            <grid2x2-plus-icon size="18" stroke-width="1" />
                            {{ t('Insert table') }}
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item small d-flex gap-1 align-items-center" @click="editor.chain().focus().deleteTable().run()" :disabled="!editor.can().deleteTable()">
                            <grid2x2-x-icon size="18" stroke-width="1" />
                            {{ t('Delete table') }}
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item small d-flex gap-1 align-items-center" @click="editor.chain().focus().fixTables().run()" :disabled="!editor.can().fixTables()">
                            <grid2x2-check-icon size="18" stroke-width="1" />
                            {{ t('Fix tables') }}
                        </button>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <button
                            type="button"
                            class="dropdown-item small d-flex gap-1 align-items-center"
                            @click="editor.chain().focus().addColumnBefore().run()"
                            :disabled="!editor.can().addColumnBefore()"
                        >
                            <between-horizontal-end-icon size="18" stroke-width="1" />
                            {{ t('Add column before') }}
                        </button>
                    </li>
                    <li>
                        <button
                            type="button"
                            class="dropdown-item small d-flex gap-1 align-items-center"
                            @click="editor.chain().focus().addColumnAfter().run()"
                            :disabled="!editor.can().addColumnAfter()"
                        >
                            <between-horizontal-start-icon size="18" stroke-width="1" />
                            {{ t('Add column after') }}
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item small d-flex gap-1 align-items-center" @click="editor.chain().focus().deleteColumn().run()" :disabled="!editor.can().deleteColumn()">
                            <x-icon size="18" stroke-width="1" />
                            {{ t('Delete column') }}
                        </button>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <button type="button" class="dropdown-item small d-flex gap-1 align-items-center" @click="editor.chain().focus().addRowBefore().run()" :disabled="!editor.can().addRowBefore()">
                            <between-vertical-end-icon size="18" stroke-width="1" />
                            {{ t('Add row before') }}
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item small d-flex gap-1 align-items-center" @click="editor.chain().focus().addRowAfter().run()" :disabled="!editor.can().addRowAfter()">
                            <between-vertical-start-icon size="18" stroke-width="1" />
                            {{ t('Add row after') }}
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item small d-flex gap-1 align-items-center" @click="editor.chain().focus().deleteRow().run()" :disabled="!editor.can().deleteRow()">
                            <x-icon size="18" stroke-width="1" />
                            {{ t('Delete row') }}
                        </button>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <button type="button" class="dropdown-item small d-flex gap-1 align-items-center" @click="editor.chain().focus().mergeCells().run()" :disabled="!editor.can().mergeCells()">
                            <table-cells-merge-icon size="18" stroke-width="1" />
                            {{ t('Merge cells') }}
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item small d-flex gap-1 align-items-center" @click="editor.chain().focus().splitCell().run()" :disabled="!editor.can().splitCell()">
                            <table-cells-split-icon size="18" stroke-width="1" />
                            {{ t('Split cell') }}
                        </button>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <button
                            type="button"
                            class="dropdown-item small d-flex gap-1 align-items-center"
                            @click="editor.chain().focus().toggleHeaderColumn().run()"
                            :disabled="!editor.can().toggleHeaderColumn()"
                        >
                            {{ t('Toggle header column') }}
                        </button>
                    </li>
                    <li>
                        <button
                            type="button"
                            class="dropdown-item small d-flex gap-1 align-items-center"
                            @click="editor.chain().focus().toggleHeaderRow().run()"
                            :disabled="!editor.can().toggleHeaderRow()"
                        >
                            {{ t('Toggle header row') }}
                        </button>
                    </li>
                    <li>
                        <button
                            type="button"
                            class="dropdown-item small d-flex gap-1 align-items-center"
                            @click="editor.chain().focus().toggleHeaderCell().run()"
                            :disabled="!editor.can().toggleHeaderCell()"
                        >
                            {{ t('Toggle header cell') }}
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tiptap-separator"></div>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Code Block')"
                v-tooltip
                @click="editor.chain().focus().toggleCodeBlock().run()"
                :class="{ 'is-active': editor.isActive('codeBlock') }"
                :disabled="editor.isActive('image')"
            >
                <square-code-icon size="18" stroke-width="1.5" />
            </button>
            <button
                type="button"
                class="tiptap-button"
                :title="t('Code')"
                v-tooltip
                @click="editor.chain().focus().toggleCode().run()"
                :disabled="!editor.can().chain().focus().toggleCode().run()"
                :class="{ 'is-active': editor.isActive('code') }"
            >
                <code-icon size="18" stroke-width="1.5" />
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" class="tiptap-button" :title="t('Insert Horizontal Line')" v-tooltip @click="editor.chain().focus().setHorizontalRule().run()">
                <minus-icon size="18" stroke-width="1.5" />
            </button>
            <button type="button" class="tiptap-button" :title="t('Insert Line Break')" v-tooltip @click="editor.chain().focus().setHardBreak().run()">
                <wrap-text-icon size="18" stroke-width="1.5" />
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" class="tiptap-button" :title="t('Undo')" v-tooltip @click="editor.chain().focus().undo().run()" :disabled="!editor.can().chain().focus().undo().run()">
                <undo-icon size="18" stroke-width="1.5" />
            </button>
            <button type="button" class="tiptap-button" :title="t('Redo')" v-tooltip @click="editor.chain().focus().redo().run()" :disabled="!editor.can().chain().focus().redo().run()">
                <redo-icon size="18" stroke-width="1.5" />
            </button>
            <div class="tiptap-separator"></div>
            <button type="button" class="tiptap-button" :title="t('Edit Source Code')" v-tooltip @click="openSourceCodeDialog">
                <file-code-icon size="18" stroke-width="1.5" />
            </button>
        </div>
        <bubble-menu :editor="editor" v-if="editor">
            <div class="bubble-menu btn-group shadow">
                <button
                    v-if="!editor.isActive('image')"
                    type="button"
                    class="tiptap-button"
                    :title="t('Bold')"
                    v-tooltip
                    @click="editor.chain().focus().toggleBold().run()"
                    :class="{ 'is-active': editor.isActive('bold') }"
                >
                    <bold-icon size="18" stroke-width="1.5" />
                </button>
                <button
                    v-if="!editor.isActive('image')"
                    type="button"
                    class="tiptap-button"
                    :title="t('Italic')"
                    v-tooltip
                    @click="editor.chain().focus().toggleItalic().run()"
                    :class="{ 'is-active': editor.isActive('italic') }"
                >
                    <italic-icon size="18" stroke-width="1.5" />
                </button>
                <button v-if="!editor.isActive('image')" type="button" class="tiptap-button" :title="t('Link')" v-tooltip @click="openLinkDialog" :class="{ 'is-active': editor.isActive('link') }">
                    <link2-icon size="18" stroke-width="1.5" />
                </button>
                <button type="button" class="tiptap-button" :title="t('Unset Link')" v-tooltip @click="editor.chain().focus().unsetLink().run()" v-if="editor.isActive('link')">
                    <link2-off-icon size="18" stroke-width="1.5" />
                </button>
                <button type="button" class="tiptap-button" :title="t('Edit')" v-tooltip @click="openImageDialog" v-if="editor.isActive('image')">Edit</button>
            </div>
        </bubble-menu>
        <editor-content :editor="editor" :data-language="locale" />
        <textarea :name="name" class="d-none" v-if="editor">{{ editor.getHTML() }}</textarea>
        <tiptap-link-dialog :id="'link-dialog-' + id + '-' + locale" v-model:link="link" v-model:show="linkDialogOpened" @save="setLink"></tiptap-link-dialog>
        <tiptap-image-dialog :id="'image-dialog-' + id + '-' + locale" v-model:image="image" v-model:captioned="imageCaptioned" v-model:show="imageDialogOpened" @save="setImage"></tiptap-image-dialog>
        <tiptap-iframe-dialog :id="'youtube-dialog-' + id + '-' + locale" v-model:video="youtube" v-model:show="youtubeDialogOpened" @save="addYoutube" title="YouTube Video"></tiptap-iframe-dialog>
        <tiptap-iframe-dialog :id="'iframe-dialog-' + id + '-' + locale" v-model:video="iframe" v-model:show="iframeDialogOpened" @save="addIframe" title="Media embed"></tiptap-iframe-dialog>
        <tiptap-source-code-dialog
            :id="'source-code-dialog-' + id + '-' + locale"
            v-model:html="sourceCodeHtml"
            v-model:show="sourceCodeDialogOpened"
            @save="setSourceCode"
        ></tiptap-source-code-dialog>
    </div>
</template>

<script setup>
import Image from '@tiptap/extension-image';
import { BulletList } from '@tiptap/extension-list';
import Paragraph from '@tiptap/extension-paragraph';
import Subscript from '@tiptap/extension-subscript';
import Superscript from '@tiptap/extension-superscript';
import { TableKit } from '@tiptap/extension-table';
import TextAlign from '@tiptap/extension-text-align';
import Typography from '@tiptap/extension-typography';
import Youtube from '@tiptap/extension-youtube';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { BubbleMenu } from '@tiptap/vue-3/menus';
import Tooltip from 'bootstrap/js/dist/tooltip';
import {
    AlignCenterIcon,
    AlignLeftIcon,
    AlignRightIcon,
    BetweenHorizontalEndIcon,
    BetweenHorizontalStartIcon,
    BetweenVerticalEndIcon,
    BetweenVerticalStartIcon,
    BoldIcon,
    CodeIcon,
    FileCodeIcon,
    Grid2x2CheckIcon,
    Grid2x2PlusIcon,
    Grid2x2XIcon,
    ImageIcon,
    IndentDecreaseIcon,
    IndentIncreaseIcon,
    ItalicIcon,
    Link2Icon,
    Link2OffIcon,
    ListIcon,
    ListOrderedIcon,
    MessageSquareQuoteIcon,
    MinusIcon,
    RedoIcon,
    RemoveFormattingIcon,
    SheetIcon,
    SquareCodeIcon,
    StrikethroughIcon,
    SubscriptIcon,
    SuperscriptIcon,
    TableCellsMergeIcon,
    TableCellsSplitIcon,
    TextSelectIcon,
    UndoIcon,
    VideoIcon,
    WrapTextIcon,
    XIcon,
    YoutubeIcon,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

import { Div } from './div.ts';
import { Figcaption } from './figcaption.ts';
import { Figure } from './figure.ts';
import { Iframe } from './iframe.ts';
import TiptapIframeDialog from './TiptapIframeDialog.vue';
import TiptapImageDialog from './TiptapImageDialog.vue';
import TiptapLinkDialog from './TiptapLinkDialog.vue';
import TiptapSourceCodeDialog from './TiptapSourceCodeDialog.vue';

const { t } = useI18n();

const link = ref({});
const linkDialogOpened = ref(false);

const image = ref({});
const imageCaptioned = ref(false);
const imageDialogOpened = ref(false);

const youtube = ref({});
const youtubeDialogOpened = ref(false);

const iframe = ref({});
const iframeDialogOpened = ref(false);

const sourceCodeHtml = ref('');
const sourceCodeDialogOpened = ref(false);

const id = computed(() => {
    return String(props.name).replace(/[^a-z0-9]/g, '');
});

const props = defineProps({
    name: {
        type: String,
    },
    label: {
        type: String,
        default: null,
    },
    locale: {
        type: String,
    },
    initContent: {
        type: String,
        default: '',
    },
});

const transformHtml = (html) => {
    if (!html) {
        return html;
    }

    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');

    // Find all iframes
    const iframes = doc.querySelectorAll('iframe');

    iframes.forEach((iframe) => {
        const parent = iframe.parentElement;

        if (!parent) {
            return;
        }

        // Check if iframe is from YouTube
        const src = iframe.getAttribute('src') || '';
        const isYoutube = src.includes('youtube.com') || src.includes('youtu.be');

        // Determine the data attribute to add
        const dataAttr = isYoutube ? 'data-youtube-video' : 'data-media-embed';

        // If parent is a <p> tag, replace it with a <div>
        if (parent.tagName.toLowerCase() === 'p') {
            const div = doc.createElement('div');

            // Copy all attributes from p to div
            Array.from(parent.attributes).forEach((attr) => {
                div.setAttribute(attr.name, attr.value);
            });

            // Add the data attribute
            div.setAttribute(dataAttr, '');

            // Move all children from p to div
            while (parent.firstChild) {
                div.appendChild(parent.firstChild);
            }

            // Replace p with div
            parent.replaceWith(div);
        } else if (parent.tagName.toLowerCase() === 'div') {
            // If parent is already a div, just add the data attribute
            parent.setAttribute(dataAttr, '');
        }
    });

    return doc.body.innerHTML;
};

const content = ref(transformHtml(props.initContent));

const blockStyles = [
    { tag: 'paragraph', class: 'lead', label: 'Lead Paragraph' },
    { tag: 'paragraph', class: 'alert alert-info', label: 'Alert Info' },
    { tag: 'paragraph', class: 'alert alert-warning', label: 'Alert Warning' },
    { tag: 'paragraph', class: 'alert alert-success', label: 'Alert Success' },
    { tag: 'paragraph', class: 'alert alert-danger', label: 'Alert Danger' },
    // { tag: 'bulletList', class: 'list-columns', label: 'List in columns' },
    // { tag: 'div', class: 'header', label: 'Header' },
];

const linkStyles = [
    { tag: 'a', class: 'btn-primary', label: 'Button Primary' },
    { tag: 'a', class: 'btn-secondary', label: 'Button Secondary' },
    { tag: 'a', class: 'btn-white', label: 'Button White' },
    { tag: 'a', class: 'btn-outline-primary', label: 'Button Outline Primary' },
    { tag: 'a', class: 'btn-outline-secondary', label: 'Button Outline Secondary' },
];

const CustomParagraph = Paragraph.extend({
    addAttributes() {
        return {
            class: {
                default: null,
                renderHTML: (attributes) => {
                    return {
                        class: attributes.class || null,
                    };
                },
            },
        };
    },
});

const CustomBulletList = BulletList.extend({
    addAttributes() {
        return {
            class: {
                default: null,
                renderHTML: (attributes) => {
                    return {
                        class: attributes.class || null,
                    };
                },
            },
        };
    },
});

const CustomImage = Image.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            style: {
                default: null,
                parseHTML: (element) => element.getAttribute('style'),
                renderHTML: (attributes) => {
                    if (!attributes.style) {
                        return {};
                    }

                    return {
                        style: attributes.style,
                    };
                },
            },
        };
    },
});

const editor = useEditor({
    extensions: [
        StarterKit.configure({
            listKeymap: false,
            underline: false,
            trailingNode: false,
            paragraph: false,
            bulletList: false,
            link: {
                openOnClick: false,
                defaultProtocol: 'https',
                HTMLAttributes: {
                    rel: null,
                    target: null,
                },
            },
        }),
        CustomBulletList,
        Typography,
        Superscript,
        Subscript,
        Youtube.configure({
            nocookie: true,
            modestBranding: true,
            width: 560,
            height: 315,
        }),
        Iframe.configure({
            width: 560,
            height: 315,
        }),
        Figure,
        Figcaption,
        CustomImage,
        CustomParagraph,
        Div,
        TableKit.configure({
            table: { resizable: false },
        }),
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
    ],
    content: content.value,
});

const openLinkDialog = function () {
    linkDialogOpened.value = true;
    link.value = {
        href: editor.value.getAttributes('link').href,
        target: editor.value.getAttributes('link').target,
    };
};

const setLink = function () {
    if (link.value.href === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
    } else {
        editor.value.chain().focus().extendMarkRange('link').setLink(link.value).run();
    }
};

const openImageDialog = function () {
    imageDialogOpened.value = true;
    const isFigure = editor.value.view.state.selection.$head.parent.type.name === 'figure';
    imageCaptioned.value = isFigure;

    const imageAttrs = editor.value.getAttributes('image');
    const figureAttrs = isFigure ? editor.value.getAttributes('figure') : {};

    // Determine alignment from style attribute
    const style = isFigure ? figureAttrs.style : imageAttrs.style;
    let align = 'none';
    if (style) {
        if (style.includes('float:left') || style.includes('float: left')) {
            align = 'left';
        } else if (style.includes('float:right') || style.includes('float: right')) {
            align = 'right';
        }
    }

    image.value = {
        src: imageAttrs.src,
        alt: imageAttrs.alt,
        width: imageAttrs.width,
        height: imageAttrs.height,
        align: align,
    };
};

const setImage = function () {
    if (image.value.src) {
        // Determine style based on alignment
        const style = image.value.align === 'left' ? 'float:left' : image.value.align === 'right' ? 'float:right' : null;

        if (editor.value.view.state.selection.$head.parent.type.name !== 'figure') {
            // If the current image is not in a figure…
            if (imageCaptioned.value) {
                // Add figure and figcaption with style.
                editor.value
                    .chain()
                    .focus()
                    .insertContent({
                        type: 'figure',
                        attrs: {
                            style: style,
                        },
                        content: [
                            {
                                type: 'image',
                                attrs: {
                                    src: image.value.src,
                                    alt: image.value.alt,
                                    width: image.value.width,
                                    height: image.value.height,
                                },
                            },
                            {
                                type: 'figcaption',
                                content: [
                                    {
                                        type: 'text',
                                        text: '…',
                                    },
                                ],
                            },
                        ],
                    })
                    .run();
            } else {
                // Just insert the image with style.
                editor.value
                    .chain()
                    .focus()
                    .setImage({
                        src: image.value.src,
                        alt: image.value.alt,
                        width: image.value.width,
                        height: image.value.height,
                        style: style,
                    })
                    .run();
            }
        } else {
            // If the current image is in a figure…
            if (imageCaptioned.value) {
                // Update image attributes
                editor.value
                    .chain()
                    .focus()
                    .setImage({
                        src: image.value.src,
                        alt: image.value.alt,
                        width: image.value.width,
                        height: image.value.height,
                    })
                    .run();
                // Update figure style
                editor.value.commands.updateAttributes('figure', { style: style });
            } else {
                // Replace the figure with an image.
                editor.value
                    .chain()
                    .focus()
                    .selectParentNode()
                    .insertContent({
                        type: 'image',
                        attrs: {
                            src: image.value.src,
                            alt: image.value.alt,
                            width: image.value.width,
                            height: image.value.height,
                            style: style,
                        },
                    })
                    .run();
            }
        }
    }
};

const openYoutubeDialog = function () {
    youtubeDialogOpened.value = true;
    youtube.value = {};
};

const addYoutube = function () {
    editor.value.commands.setYoutubeVideo({
        src: youtube.value.src,
    });
};

const openIframeDialog = function () {
    iframeDialogOpened.value = true;
    iframe.value = {};
};

const addIframe = function () {
    editor.value.commands.insertContent({
        type: 'iframe',
        attrs: {
            src: iframe.value.src,
            width: 560,
            height: 315,
        },
    });
};

const openSourceCodeDialog = function () {
    sourceCodeDialogOpened.value = true;
    sourceCodeHtml.value = editor.value.getHTML();
};

const setSourceCode = function () {
    if (sourceCodeHtml.value !== null && sourceCodeHtml.value !== undefined) {
        editor.value.commands.setContent(sourceCodeHtml.value);
    }
};

const vTooltip = {
    mounted: (element) => {
        new Tooltip(element, { delay: { show: 500, hide: 0 }, trigger: 'hover' });
    },
};
</script>
