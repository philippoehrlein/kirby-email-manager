<?php
namespace KirbyEmailManager\Helpers;
use KirbyEmailManager\Helpers\UrlHelper;
use KirbyEmailManager\Helpers\LanguageHelper;
use KirbyEmailManager\EmailTemplate;
use Exception;

/**
 * Helper class for email-related operations.
 * Author: Philipp Oehrlein
 * Version: 1.0.0
 */
class EmailHelper {

    protected static ?LanguageHelper $languageHelper = null;

    /**
     * Initializes the language helper.
     *
     * @param array $templateConfig The template configuration.
     * @param string|null $languageCode The language code.
     * @return LanguageHelper The language helper instance.
     */
    protected static function initLanguageHelper($templateConfig, $languageCode = null): LanguageHelper 
    {
        if (self::$languageHelper === null) {
            $languageCode = $languageCode ?? kirby()->languageCode() ?? 'en';
            self::$languageHelper = new LanguageHelper($languageCode, $templateConfig);
        }
        return self::$languageHelper;
    }

    /**
     * Sends an email based on the provided configuration and data.
     *
     * @param \Kirby\Cms\App $kirby The Kirby application instance.
     * @param \Kirby\Cms\ContentWrapper $contentWrapper The content wrapper instance.
     * @param \Kirby\Cms\Page $page The current page instance.
     * @param array $templateConfig The configuration for the email template.
     * @param array $data The form data.
     * @param string $languageCode The language code.
     * @param array $attachments Array of file paths to attach to the email.
     * @param string $subject The email subject line.
     * @throws \Exception If there's an error sending the email.
     */
    public static function sendEmail($kirby, $contentWrapper, $page, $templateConfig, $data, $languageCode, $attachments, $subject) {
        self::initLanguageHelper($templateConfig, $languageCode);

        $to = self::getReceiverEmail($contentWrapper, $data);
        $subject = self::getEmailSubject($contentWrapper, $data, $templateConfig);
        $selectedTemplate = $contentWrapper->email_templates();

        $templatePath = $kirby->root('site') . '/templates/emails/' . $selectedTemplate;

        $receiverTemplate = $templateConfig['templates']['receiver'] ?? '';
        $templatePath = $selectedTemplate . '/' . $receiverTemplate;

        $senderName = self::getEmailSender($templateConfig, $data);
        $senderEmail = self::getFromEmail($templateConfig, $data, $kirby);

        $formSenderName = self::getFormSender($templateConfig);
        $formSenderEmail = self::createNoReplyEmail($kirby);

        $footerContent = null;
        if ($contentWrapper) {
            $footer = $contentWrapper->email_legal_footer();
            if (!empty($footer)) {
                $footerContent = UrlHelper::convertLinksToAbsolute($footer, $kirby);
            }
        }

        $emailTemplate = new EmailTemplate($page, $data, $footerContent, $selectedTemplate, $templateConfig);

        try {
            $kirby->email([
                'template' => $templatePath,
                'from'     => [$formSenderEmail => $formSenderName],
                'replyTo'  => $senderEmail,
                'to'       => $to,
                'subject'  => $subject,
                'attachments' => $attachments,
                'data'     => [
                    'email' => $emailTemplate->content(),
                    'form' => $emailTemplate->form(),
                    'languageCode' => $languageCode
                ]
            ]);
            
        } catch (Exception $e) {
            error_log('Fehler beim Senden der E-Mail: ' . $e->getMessage());
            throw $e;
        }

        if (isset($templateConfig['templates']['confirmation']) && isset($data['email'])) {
            $confirmationTemplate = $templateConfig['templates']['confirmation'] ?? '';
            $confirmationHtmlTemplate = $confirmationTemplate . '.html.php' ?? null;
            $confirmationTextTemplate = $confirmationTemplate . '.text.php' ?? null;
        
            $confirmationTemplatePath = $kirby->root('site') . '/templates/emails/' . $selectedTemplate;
        
            if (!file_exists($confirmationTemplatePath . '/' . $confirmationTextTemplate)) {
                throw new Exception(t('error_messages.confirmation_template_not_found', 'Confirmation email template not found: ') . $confirmationTemplatePath . '/[' . $confirmationTextTemplate . ']');
            }
        
            $confirmationTemplatePath = $selectedTemplate . '/' . $confirmationTemplate;
        
            $confirmationSubject = self::getConfirmationSubject($templateConfig);
            
            $kirby->email([
                'template' => $confirmationTemplatePath,
                'from'     => [$formSenderEmail => $formSenderName],
                'to'       => $data['email'],
                'subject'  => $confirmationSubject,
                'data'     => [
                    'email' => $emailTemplate->content(),
                    'form' => $emailTemplate->form(),
                    'languageCode' => $languageCode
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
     * Retrieves the email subject based on the content wrapper, form data and template configuration.
     *
     * @param \Kirby\Cms\Content $contentWrapper The content wrapper instance.
     * @param array $data The form data.
     * @param array $templateConfig The configuration for the email template.
     * @return string The email subject.
     */
    public static function getEmailSubject($contentWrapper, $data, $templateConfig) {
        $languageHelper = self::initLanguageHelper($templateConfig);
        
        // Wenn send_to_more aktiviert ist und ein Thema ausgewählt wurde
        if ($contentWrapper->send_to_more()->toBool() && isset($data['topic'])) {
            $templateSubject = $languageHelper->get('emails.subject.topic');
            $subject = str_replace(':topic', $data['topic'], $templateSubject);
        } else {
            // Standard E-Mail-Betreff
            $subject = $languageHelper->get('emails.subject.default');
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
        // Try to get the email address from the configuration first
        $email = option('email.noreply');
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // If no valid email address is configured, try to create it from the site URL
            $host = parse_url($kirby->url(), PHP_URL_HOST);
            
            // Remove port information if present
            $host = preg_replace('/:\d+$/', '', $host);
            
            // Remove the subdomain if present
            $hostParts = explode('.', $host);
            if (count($hostParts) > 2) {
                $host = implode('.', array_slice($hostParts, -2));
            }
            
            $email = 'no-reply@' . $host;
        }
        
        return $email;
    }

    /**
     * Retrieves the email sender name based on the template configuration and form data.
     *
     * @param array $templateConfig The configuration for the email template.
     * @param array $data The form data.
     * @return string The email sender name.
     */
    public static function getEmailSender($templateConfig, $data) {
        foreach ($templateConfig['fields'] as $fieldKey => $fieldConfig) {
            if (isset($fieldConfig['is_from_name_field']) && $fieldConfig['is_from_name_field'] === true && !empty($data[$fieldKey])) {
                return $data[$fieldKey];
            }
        }

        return self::initLanguageHelper($templateConfig)->get('emails.sender');
    }

    /**
     * Retrieves the confirmation subject based on the template configuration.
     *
     * @param array $templateConfig The configuration for the email template.
     * @return string The confirmation subject.
     */
    public static function getConfirmationSubject($templateConfig) {
        return self::initLanguageHelper($templateConfig)->get('emails.confirmation.subject');
    }
    
    /**
     * Retrieves the confirmation sender based on the template configuration.
     *
     * @param array $templateConfig The configuration for the email template.
     * @return string The confirmation sender.
     */
    public static function getFormSender($templateConfig) {
        return self::initLanguageHelper($templateConfig)->get('emails.confirmation.sender');
    }
}