<?php

namespace KirbyEmailManager\Helpers;

use KirbyEmailManager\PageMethods\ContentWrapper;

/**
 * SuccessMessageHelper class for managing success messages
 * 
 * This class provides methods to retrieve success messages based on the content wrapper and data.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class SuccessMessageHelper
{
    /**
     * Retrieves the success message based on the content wrapper and data.
     * 
     * @param ContentWrapper $contentWrapper The content wrapper object.
     * @param array $data The data array.
     * @param string $languageCode The language code.
     * @return array The success message.
     */
    public static function getSuccessMessage(ContentWrapper $contentWrapper, array $data, string $languageCode): array
    {
        if ($contentWrapper->send_to_more()->toBool() && isset($data['topic'])) {
            $topic = $data['topic'];
            $successStructure = $contentWrapper->send_to_structure()->toStructure()->findBy('topic', $topic);
            if ($successStructure) {
                return [
                    'title' => $successStructure->success_title()->value(),
                    'text' => $successStructure->success_text()->value()
                ];
            }
        }

        return [
            'title' => $contentWrapper->send_to_success_title()->value(),
            'text' => $contentWrapper->send_to_success_text()->value()
        ];
    }
}