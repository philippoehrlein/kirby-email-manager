<?php

namespace KirbyEmailManager\Helpers;

use Kirby\Cms\App as Kirby;
use KirbyEmailManager\Helpers\UrlHelper;

class LegalHelper
{
    /**
     * Get the email footer (local or global).
     *
     * @param \Kirby\Cms\Content $contentWrapper
     * @param string             $languageCode
     * @param Kirby              $kirby
     * @return string|null
     */
    public static function getEmailFooter($contentWrapper, string $languageCode, Kirby $kirby): ?string
    {
        if (self::usesIndividualLegal($contentWrapper)) {
            $footerContent = $contentWrapper->email_legal_footer();
            $footerContent = $footerContent->isNotEmpty()
                ? $footerContent
                : null;
        } else {
            $footerContent = self::getGlobalFooter($languageCode, $kirby);
        }

        return $footerContent
            ? UrlHelper::convertLinksToAbsolute($footerContent, $kirby)
            : null;
    }

    /**
     * Global footer from the site.
     */
    public static function getGlobalFooter(string $languageCode, Kirby $kirby): ?string
    {
        $site = $kirby->site();

        if ($kirby->multilang() === true && $languageCode) {
            $content = $site->content($languageCode);
        } else {
            $content = $site->content();
        }

        $field = $content->get('kem_email_legal_footer');

        if ($field && $field->isNotEmpty()) {
            return (string)$field;
        }

        return null;
    }

    /**
     * GDPR status (local or global).
     */
    public static function getGDPRStatus($contentWrapper): ?bool
    {
        if (self::usesIndividualLegal($contentWrapper)) {
            return $contentWrapper->gdpr_checkbox()->toBool();
        }

        $kirby = kirby();
        $site  = $kirby->site();

        if ($kirby->multilang() === true) {
            $content = $site->content($kirby->languageCode());
        } else {
            $content = $site->content();
        }

        $field = $content->get('kem_gdpr_checkbox');

        return $field ? $field->toBool() : null;
    }

    /**
     * GDPR text (local or global).
     */
    public static function getGDPRText($contentWrapper): ?string
    {
        if (self::usesIndividualLegal($contentWrapper)) {
            return $contentWrapper->gdpr_text()->kt()->permalinksToUrls();
        }

        $kirby = kirby();
        $site  = $kirby->site();

        if ($kirby->multilang() === true) {
            $content = $site->content($kirby->languageCode());
        } else {
            $content = $site->content();
        }

        $field = $content->get('kem_gdpr_text');

        if ($field && $field->isNotEmpty()) {
            return $field->kt()->permalinksToUrls();
        }

        return null;
    }

    /**
     * Uses individual legal settings?
     */
    protected static function usesIndividualLegal($contentWrapper): bool
    {
        if ($contentWrapper->kem_email_legal_form()->toBool() === true) {
            return true;
        }

        return false;
    }
}

