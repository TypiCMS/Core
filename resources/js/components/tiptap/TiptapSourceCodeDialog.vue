<template>
    <div class="modal fade" :id="props.id" tabindex="-1" :aria-labelledby="props.id + '-label'" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <form class="modal-content" @submit.prevent="save">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" :id="props.id + '-label'">{{ t('Edit Source Code') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" :aria-label="t('Close')"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="col-form-label">{{ t('HTML Source') }}</label>
                        <div ref="editorContainer" class="editor-container"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">{{ t('Cancel') }}</button>
                    <button type="submit" class="btn btn-sm btn-primary">{{ t('OK') }}</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { autocompletion, closeBrackets } from '@codemirror/autocomplete';
import { defaultKeymap, history, historyKeymap, indentWithTab } from '@codemirror/commands';
import { html } from '@codemirror/lang-html';
import { defaultHighlightStyle, indentOnInput, indentUnit, syntaxHighlighting } from '@codemirror/language';
import { searchKeymap } from '@codemirror/search';
import { EditorState } from '@codemirror/state';
import { drawSelection, EditorView, highlightActiveLine, highlightActiveLineGutter, keymap, lineNumbers } from '@codemirror/view';
import Modal from 'bootstrap/js/dist/modal';
import { html as beautifyHtml } from 'js-beautify';
import { onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const sourceCodeDialog = ref(null);
const activeElement = ref(null);
const editorContainer = ref(null);
const editorView = ref(null);

const show = defineModel('show', { required: true });
const htmlContent = defineModel('html', { required: true });

const props = defineProps({
    id: {
        type: String,
    },
});

const emit = defineEmits(['save']);

function initializeEditor() {
    if (!editorContainer.value) {
        return;
    }

    if (editorView.value) {
        editorView.value.destroy();
    }

    const formattedHtml = beautifyHtml(htmlContent.value || '', {
        indent_size: 4,
        indent_char: ' ',
        max_preserve_newlines: 1,
        preserve_newlines: true,
        wrap_line_length: 0,
        end_with_newline: false,
    });

    const startState = EditorState.create({
        doc: formattedHtml,
        extensions: [
            lineNumbers(),
            highlightActiveLineGutter(),
            highlightActiveLine(),
            history(),
            drawSelection(),
            EditorState.allowMultipleSelections.of(true),
            indentOnInput(),
            indentUnit.of('    '),
            syntaxHighlighting(defaultHighlightStyle),
            closeBrackets(),
            autocompletion(),
            keymap.of([...defaultKeymap, ...searchKeymap, ...historyKeymap, indentWithTab]),
            html({
                matchClosingTags: false,
            }),
            EditorView.lineWrapping,
        ],
    });

    editorView.value = new EditorView({
        state: startState,
        parent: editorContainer.value,
    });
    setTimeout(() => {
        editorView.value.focus();
    }, 500);
}

watch(
    () => show.value,
    (show) => {
        if (show) {
            activeElement.value = document.activeElement;
            sourceCodeDialog.value.show();
            initializeEditor();
        } else {
            sourceCodeDialog.value.hide();
        }
    },
);

function save() {
    if (editorView.value) {
        htmlContent.value = editorView.value.state.doc.toString();
    }
    show.value = false;
    emit('save');
}

onMounted(() => {
    sourceCodeDialog.value = new Modal('#' + props.id, {
        keyboard: false,
    });

    const modal = document.querySelector('#' + props.id);
    modal.addEventListener('hide.bs.modal', () => {
        const buttonElement = document.activeElement;
        buttonElement.blur();
    });
    modal.addEventListener('hidden.bs.modal', () => {
        show.value = false;
        if (editorView.value) {
            editorView.value.destroy();
            editorView.value = null;
        }
        if (activeElement.value) {
            activeElement.value.focus();
        }
    });
});
</script>

<style scoped>
.editor-container {
    overflow: auto;
    border-radius: var(--bs-border-radius);
    border: 1px solid var(--bs-border-color);
}
:deep(.cm-editor) {
    height: 50vh;
    font-size: 0.875rem;
}
:deep(.cm-scroller) {
    font-family: ui-monospace, SFMono-Regular, 'SF Mono', Menlo, Consolas, 'Liberation Mono', monospace;
}
</style>
