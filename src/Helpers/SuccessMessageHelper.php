<?php

namespace KirbyEmailManager\Helpers;

use KirbyEmailManager\PageMethods\ContentWrapper;

class SuccessMessageHelper
{
    public static function getSuccessMessage(ContentWrapper $contentWrapper, array $data, string $languageCode): array
    {
        if ($contentWrapper->send_to_more()->toBool() && isset($data['topic'])) {
            $topic = $data['topic'];
            $successStructure = $contentWrapper->send_to_structure()->toStructure()->findBy('topic', $topic);
            if ($successStructure) {
                return [
                    'title' => $successStructure->success()->toStructure()->first()->title()->value(),
                    'text' => $successStructure->success()->toStructure()->first()->text()->value()
                ];
            }
        }

        // Fallback to the general success message
        return [
            'title' => $contentWrapper->send_to_success_title()->value(),
            'text' => $contentWrapper->send_to_success_text()->value()
        ];
    }
}