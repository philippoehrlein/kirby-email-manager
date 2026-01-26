<?php

namespace KirbyEmailManager\Helpers;

/**
 * BlacklistHelper class for spam prevention
 * 
 * Checks form data against a configurable blacklist of terms.
 * This is a last line of defense against human spammers.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class BlacklistHelper
{
    /**
     * Checks if any form field contains a blacklisted term.
     * Uses the blacklist from Kirby config.
     *
     * @param array $data The form data to check
     * @return array ['blocked' => bool, 'matched' => string|null]
     */
    public static function check(array $data): array
    {
        $blacklist = kirby()->option('philippoehrlein.kirby-email-manager.blacklist', []);
        return self::checkAgainstList($data, $blacklist);
    }

    /**
     * Checks if any form field contains a blacklisted term.
     * Accepts blacklist as parameter for testability.
     *
     * @param array $data The form data to check
     * @param array $blacklist The list of blacklisted terms
     * @return array ['blocked' => bool, 'matched' => string|null]
     */
    public static function checkAgainstList(array $data, array $blacklist): array
    {
        if (empty($blacklist)) {
            return ['blocked' => false, 'matched' => null];
        }

        $values = self::flattenFormData($data);
        
        foreach ($blacklist as $term) {
            if (!is_string($term) || empty($term)) {
                continue;
            }
            
            $termLower = mb_strtolower($term);
            
            foreach ($values as $value) {
                if (mb_stripos($value, $termLower) !== false) {
                    return [
                        'blocked' => true,
                        'matched' => $term
                    ];
                }
            }
        }
        
        return ['blocked' => false, 'matched' => null];
    }

    /**
     * Flattens form data to an array of string values.
     * Handles nested arrays (e.g., date-range fields).
     *
     * @param array $data The form data
     * @return array Flat array of string values (lowercase)
     */
    public static function flattenFormData(array $data): array
    {
        $values = [];
        
        foreach ($data as $key => $value) {
            // Skip system fields
            if (in_array($key, ['csrf', 'timestamp', 'submit', 'website_hp_', 'gdpr'])) {
                continue;
            }
            
            if (is_array($value)) {
                foreach ($value as $subValue) {
                    if (is_string($subValue) && !empty($subValue)) {
                        $values[] = mb_strtolower($subValue);
                    }
                }
            } elseif (is_string($value) && !empty($value)) {
                $values[] = mb_strtolower($value);
            }
        }
        
        return $values;
    }
}
