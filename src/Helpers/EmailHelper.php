<?php
namespace KirbyEmailManager\Helpers;
use KirbyEmailManager\Helpers\UrlHelper;
/**
 * Helper class for email-related operations.
 * Author: Philipp Oehrlein
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
    public static function sendEmail($kirby, $contentWrapper, $page, $templateConfig, $emailContent, $data, $languageCode, $attachments) {
        $preferredLanguage = $templateConfig['preferred_language'] ?? $languageCode;

        $to = self::getReceiverEmail($contentWrapper, $data);
        $subject = self::getEmailSubject($contentWrapper, $data, $templateConfig, $languageCode);
        $selectedTemplate = $contentWrapper->email_templates();

        $templatePath = $kirby->root('site') . '/templates/emails/' . $selectedTemplate;

        $receiverTemplate = $templateConfig['templates']['receiver'] ?? '';
        $templatePath = $selectedTemplate . '/' . $receiverTemplate;

        $senderName = self::getEmailSender($templateConfig, $languageCode);
        $senderEmail = self::getFromEmail($templateConfig, $data, $kirby);

        $formSenderName = self::getFormSender($templateConfig, $languageCode);
        $formSenderEmail = self::createNoReplyEmail($kirby);
        // error_log('Attachments: ' . print_r($attachments, true));

        try {
            $kirby->email([
                'template' => $templatePath, 
                'from'     => [$formSenderEmail => $formSenderName],
                'replyTo'  => $senderEmail,
                'to'       => $to,
                'subject'  => $subject,
                'attachments' => $attachments,
                'data'     => [
                    'formData' => $data,
                    'kirby'   => $kirby,
                    'site'    => $kirby->site(),
                    'page'    => $page,
                    'config'  => $templateConfig,
                    'languageCode' => $preferredLanguage
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
        
            $confirmationSubject = self::getConfirmationSubject($templateConfig, $languageCode);
            error_log("Original footer content: " . $contentWrapper->email_legal_footer()->kt()->value());
            $footerContent = UrlHelper::convertLinksToAbsolute($contentWrapper->email_legal_footer()->kt()->value(), $kirby) ?? null;
            

            // if (strpos($footerContent, 'x-webdoc://') !== false) {
            //     error_log("Fehler: x-webdoc Link gefunden im konvertierten Footer: " . $footerContent);
            // } else {
            //     error_log("Erfolg: Keine x-webdoc Links im konvertierten Footer.");
            // }
            
            $kirby->email([
                'template' => $confirmationTemplatePath,
                'from'     => [$formSenderEmail => $formSenderName],
                'to'       => $data['email'],
                'subject'  => $confirmationSubject,
                'data'     => [
                    'formData' => $data,
                    'kirby'   => $kirby,
                    'site'    => $kirby->site(),
                    'page'    => $page,
                    'config'  => $templateConfig,
                    'languageCode' => $languageCode,
                    'footer' => $footerContent
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
    public static function getReceiverEmail($contentWrapper, $data) {
        if ($contentWrapper->send_to_more()->toBool()) {
            $emailStructure = $contentWrapper->send_to_structure()->toStructure();
            if ($emailStructure->count() > 0 && isset($data['topic'])) {
            foreach ($emailStructure as $item) {
                if ($item->topic() == $data['topic']) {
                        return $item->email()->value();
                    }
                }
            }
        }

        return $contentWrapper->send_to()->value();
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
    public static function getEmailSubject($contentWrapper, $data, $templateConfig, $languageCode) {
        if ($contentWrapper->send_to_more()->toBool() && isset($data['topic'])) {
            $subject = $templateConfig['email_subject']['topic'] ?? $templateConfig['email_subject']['default'] ?? 'Kontaktformular Nachricht: :topic';
        } else {
            $subject = $templateConfig['email_subject']['default'] ?? 'Kontaktformular Nachricht';
        }
    
        $replacements = [
            ':topic' => $data['topic'] ?? '',
            ':langCode' => strtoupper($languageCode)
        ];
    
        // Nur die Platzhalter ersetzen, die tatsächlich im Betreff vorhanden sind
        foreach ($replacements as $placeholder => $value) {
            if (strpos($subject, $placeholder) !== false) {
                $subject = str_replace($placeholder, $value, $subject);
            }
        }
    
        return $subject;
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
        // Versuche zuerst, die E-Mail-Adresse aus der Konfiguration zu holen
        $email = option('email.noreply');
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Wenn keine gültige E-Mail in der Konfiguration, versuche sie aus der URL zu erstellen
            $host = parse_url($kirby->url(), PHP_URL_HOST);
            
            // Entferne Port-Informationen, falls vorhanden
            $host = preg_replace('/:\d+$/', '', $host);
            
            // Entferne die Subdomain, falls vorhanden
            $hostParts = explode('.', $host);
            if (count($hostParts) > 2) {
                $host = implode('.', array_slice($hostParts, -2));
            }
            
            $email = 'no-reply@' . $host;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Wenn immer noch keine gültige E-Mail, verwende einen Fallback
            $email = 'no-reply@kolk17.de';
        }
        
        return $email;
    }

    /**
     * Retrieves the email sender name based on the template configuration and language code.
     *
     * @param array $templateConfig The configuration for the email template.
     * @param string $languageCode The language code.
     * @return string The email sender name.
     */
    public static function getEmailSender($templateConfig, $languageCode) {
        return $templateConfig['email_sender'][$languageCode] 
            ?? $templateConfig['email_sender']['en'] 
            ?? 'Contact Form';
    }

    /**
     * Retrieves the confirmation subject based on the template configuration and language code.
     *
     * @param array $templateConfig The configuration for the email template.
     * @param string $languageCode The language code.
     * @return string The confirmation subject.
     */
    public static function getConfirmationSubject($templateConfig, $languageCode) {
        return $templateConfig['confirmation_subject'][$languageCode] 
            ?? $templateConfig['confirmation_subject']['en'] 
            ?? 'Confirmation of your inquiry';
    }
    
    /**
     * Retrieves the confirmation sender based on the template configuration and language code.
     *
     * @param array $templateConfig The configuration for the email template.
     * @param string $languageCode The language code.
     * @return string The confirmation sender.
     */
    public static function getFormSender($templateConfig, $languageCode) {
        return "no-reply@kolk17.de";
    }
}