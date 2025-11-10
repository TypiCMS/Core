import { mergeAttributes, Node, nodeInputRule } from '@tiptap/core';

export interface IframeOptions {
    allowFullscreen: boolean;
    HTMLAttributes: Record<string, unknown>;
}

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        iframe: {
            setIframe: (options: { src: string }) => ReturnType;
        };
    }
}

export const Iframe = Node.create<IframeOptions>({
    name: 'iframe',

    addOptions() {
        return {
            allowFullscreen: true,
            HTMLAttributes: {},
        };
    },

    inline: false,

    group: 'block',

    draggable: true,

    atom: true,

    addAttributes() {
        return {
            src: {
                default: null,
            },
            frameborder: {
                default: 0,
            },
            allowfullscreen: {
                default: this.options.allowFullscreen,
                parseHTML: () => this.options.allowFullscreen,
            },
        };
    },

    parseHTML() {
        return [
            {
                tag: 'div[data-iframe-wrapper]',
                getAttrs: (element) => {
                    if (typeof element === 'string') {
                        return false;
                    }
                    const iframe = element.querySelector('iframe');
                    if (!iframe) {
                        return false;
                    }

                    return {
                        src: iframe.getAttribute('src'),
                        frameborder: iframe.getAttribute('frameborder') || 0,
                        allowfullscreen: iframe.hasAttribute('allowfullscreen'),
                    };
                },
            },
        ];
    },

    renderHTML({ HTMLAttributes }) {
        return [
            'div',
            mergeAttributes(this.options.HTMLAttributes, { 'data-iframe-wrapper': '' }),
            [
                'iframe',
                mergeAttributes({
                    width: 560,
                    height: 315,
                    frameborder: '0',
                    allowfullscreen: this.options.allowFullscreen ? 'true' : null,
                    ...HTMLAttributes,
                }),
            ],
        ];
    },

    addCommands() {
        return {
            setIframe:
                (options: { src: string }) =>
                ({ commands }) => {
                    return commands.insertContent({
                        type: this.name,
                        attrs: options,
                    });
                },
        };
    },
});
