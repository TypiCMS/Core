<?php

namespace TypiCMS\Modules\Core\Observers;

use DOMDocument;
use DOMXPath;

class TipTapHTMLObserver
{
    public function saving(mixed $model): void
    {
        if (property_exists($model, 'tipTapContent')) {
            foreach ($model->tipTapContent as $richTextElement) {
                $contents = $model->getTranslations($richTextElement);
                foreach ($contents as $locale => $content) {
                    $patchedContent = $this->patchTipTapHTML($content, $locale);
                    $model->setTranslation($richTextElement, $locale, $patchedContent);
                }
            }
        }
    }

    public function patchTipTapHTML(?string $content, string $locale): ?string
    {
        if ($content === null || $content === '<p></p>') {
            return null;
        }
        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        try {
            $dom->loadHTML(
                '<!DOCTYPE html><html lang="' . $locale . '"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><body>' . $content . '</body></html>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );
            $xpath = new DOMXPath($dom);
            $listItems = $xpath->query('//li');
            if ($listItems) {
                foreach ($listItems as $li) {
                    $paragraphs = $xpath->query('./p', $li);
                    if ($paragraphs === false) {
                        continue;
                    }
                    if ($paragraphs->length === 0) {
                        continue;
                    }
                    $p = $paragraphs->item(0);
                    while ($p->firstChild !== null) {
                        $li->insertBefore($p->firstChild, $p);
                    }
                    $li->removeChild($p);
                }
            }
            $body = $dom->getElementsByTagName('body')->item(0);
            $result = '';
            foreach ($body->childNodes as $child) {
                $result .= $dom->saveHTML($child);
            }

            return $result;
        } finally {
            libxml_clear_errors();
        }
    }
}
