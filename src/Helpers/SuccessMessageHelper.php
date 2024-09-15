<?php

namespace KirbyEmailManager\Helpers;

use Kirby\Cms\Page;

class SuccessMessageHelper
{
    public static function getSuccessMessage(Page $page, array $data, string $languageCode): array
    {
        if ($page->send_to_more()->toBool() && isset($data['topic'])) {
            $topic = $data['topic'];
            $successStructure = $page->send_to_structure()->toStructure()->findBy('topic', $topic);
            if ($successStructure) {
                return [
                    'title' => $successStructure->success()->toStructure()->first()->title()->value(),
                    'text' => $successStructure->success()->toStructure()->first()->text()->value()
                ];
            }
        }

        // Fallback auf die allgemeine Erfolgsmeldung
        return [
            'title' => $page->send_to_success_title()->value(),
            'text' => $page->send_to_success_text()->value()
        ];
    }
}