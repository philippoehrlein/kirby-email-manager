<?php
namespace KirbyEmailManager\Helpers;

/**
 * Helper class for email-related operations.
 * Author: Philip Oehrlein
 * Version: 1.0.0
 */
class EmailHelper {
    /**
     * Sends an email based on the provided configuration and data.
     *
     * @param \Kirby\Cms\App $kirby The Kirby application instance.
     * @param \Kirby\Cms\Page $page The current page instance.
     * @param array $templateConfig The configuration for the email template.
     * @param string $emailContent The content of the email.
     * @param array $data The form data.
     * @param string $languageCode The language code.  
     * @throws \Exception If there's an error sending the email.
     */
    public static function sendEmail($kirby, $page, $templateConfig, $emailContent, $data, $languageCode) {
        $to = self::getReceiverEmail($page, $data);
        $subject = self::getEmailSubject($page, $data, $templateConfig, $languageCode);
        $selectedTemplate = $page->email_templates()->value();

        $templatePath = $kirby->root('site') . '/templates/emails/' . $selectedTemplate;

        $receiverTemplate = $templateConfig['templates']['receiver'] ?? '';
        $templatePath = $selectedTemplate . '/' . $receiverTemplate;

        try {
            $kirby->email([
                'template' => $templatePath, 
                'from'     => self::getFromEmail($templateConfig, $data, $kirby),
                'replyTo'  => self::getFromEmail($templateConfig, $data, $kirby),
                'to'       => $to,
                'subject'  => $subject,
                'data'     => [
                    'formData' => $data,
                    'kirby'   => $kirby,
                    'site'    => $kirby->site(),
                    'page'    => $page,
                ]
            ]);
        } catch (Exception $e) {
            throw $e;
        }

        if (isset($templateConfig['templates']['confirmation']) && isset($data['email'])) {
            $confirmationTemplate = $templateConfig['templates']['confirmation'] ?? '';
            $confirmationHtmlTemplate = $confirmationTemplate . '.html.php' ?? null;
            $confirmationTextTemplate = $confirmationTemplate . '.text.php' ?? null;
        
            $confirmationTemplatePath = $kirby->root('site') . '/templates/emails/' . $selectedTemplate;
        
            if (!file_exists($confirmationTemplatePath . '/' . $confirmationTextTemplate)) {
                throw new Exception('Confirmation email template not found: ' . $confirmationTemplatePath . '/[' . $confirmationTextTemplate . ']');
            }
        
            $confirmationTemplatePath = $selectedTemplate . '/' . $confirmationTemplate;
        
        
            $kirby->email([
                'template' => $confirmationTemplatePath,
                'from'     => self::createNoReplyEmail($kirby),
                'to'       => $data['email'],
                'subject'  => $templateConfig['translations'][$languageCode]['confirmation_subject'] ?? 'Confirmation',
                'data'     => [
                    'formData' => $data,
                    'kirby'   => $kirby,
                    'site'    => $kirby->site(),
                    'page'    => $page,
                ]
            ]);
        }
    }

    /**
     * Sets the receiver email address based on the page and data.
     *
     * @param \Kirby\Cms\Page $page The current page instance.
     * @param array $data The form data.
     * @return string The receiver email address.
     */
    public static function getReceiverEmail($page, $data) {
        if ($page->send_to_more()->toBool()) {
            $emailStructure = $page->send_to_structure()->toStructure();
            if ($emailStructure->count() > 0 && isset($data['topic'])) {
            foreach ($emailStructure as $item) {
                if ($item->topic() == $data['topic']) {
                        return $item->email()->value();
                    }
                }
            }
        }

        return $page->send_to()->value();
    }

    /**
     * Retrieves the email subject based on the page, data, template configuration, and language code.
     *
     * @param \Kirby\Cms\Page $page The current page instance.
     * @param array $data The form data.
     * @param array $templateConfig The configuration for the email template.
     * @param string $languageCode The language code.
     * @return string The email subject.
     */
    public static function getEmailSubject($page, $data, $templateConfig, $languageCode) {
    if ($page->send_to_more()->toBool() && isset($data['topic'])) {
        return $data['topic'];
    }
    return $templateConfig['email_subject'][$languageCode] ?? $templateConfig['email_subject']['en'] ?? 'Kontaktformular Nachricht';
    }

    /**
     * Retrieves the from email address based on the template configuration, data, and Kirby instance.
     *
     * @param array $templateConfig The configuration for the email template.
     * @param array $data The form data.
     * @param \Kirby\Cms\App $kirby The Kirby application instance.
     * @return string The from email address.
     */
    public static function getFromEmail($templateConfig, $data, $kirby) {
        foreach ($templateConfig['fields'] as $fieldKey => $fieldConfig) {
            if (isset($fieldConfig['is_from_field']) && $fieldConfig['is_from_field'] === true && !empty($data[$fieldKey])) {
                return $data[$fieldKey];
            }
        }
    
        $configuredFrom = option('email.from');
        if ($configuredFrom) {
            return $configuredFrom;
        }
    
        return self::createNoReplyEmail($kirby);
    }

    /**
     * Creates a no-reply email address based on the site URL.
     *
     * @param \Kirby\Cms\App $kirby The Kirby application instance.
     * @return string The no-reply email address.
     */
    public static function createNoReplyEmail($kirby) {
        $siteUrl = $kirby->site()->url();
        $host = parse_url($siteUrl, PHP_URL_HOST);
        return 'no-reply@' . $host;
    }
}