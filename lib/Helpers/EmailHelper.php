<?php
namespace KirbyEmailManager\Helpers;
use KirbyEmailManager\Helpers\UrlHelper;
use KirbyEmailManager\Helpers\LanguageHelper;
use KirbyEmailManager\Services\EmailTemplate;
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
     * @param \KirbyEmailManager\PageMethods\ContentWrapper $contentWrapper The content wrapper instance.
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

        $to = self::getToEmail($contentWrapper, $data);
        $subject = self::getEmailSubject($contentWrapper, $data, $templateConfig);
        $selectedTemplate = $contentWrapper->email_templates();
        $replyToEmail = self::getReplyToEmail($templateConfig, $data);
        $formSenderName = self::getFormSender($templateConfig);
        $formSenderEmail = self::createNoReplyEmail($kirby);

        // Get footer content if available
        $footerContent = null;
        if ($contentWrapper->email_legal_footer()->notEmpty()) {
            $footer = $contentWrapper->email_legal_footer();
            if (!empty($footer)) {
                $footerContent = UrlHelper::convertLinksToAbsolute($footer, $kirby);
            }
        }

        $emailTemplate = new EmailTemplate($page, $data, $footerContent, $selectedTemplate, $templateConfig);
        $emailTemplate->addSubject($subject);

        // Send main email
        $emailConfig = [
            'template' => $selectedTemplate . '/mail',
            'from'     => [$formSenderEmail => $formSenderName],
            'to'       => $to,
            'subject'  => $subject,
            'attachments' => $attachments,
            'data'     => [
                'email' => $emailTemplate->content(),
                'form' => $emailTemplate->form(),
                'languageCode' => $languageCode
            ]
        ];

        if ($replyToEmail !== null) {
            $emailConfig['replyTo'] = $replyToEmail;
        }

        try {
            $kirby->email($emailConfig);
        } catch (Exception $e) {
            error_log('Error sending main email: ' . $e->getMessage());
            throw $e;
        }
        
        // Check if reply should be sent
        $replyPath = $kirby->root('templates') . '/emails/' . $selectedTemplate . '/reply.text.php';
        $replyEmail = self::getReplyEmail($templateConfig, $data);

        $hasReplyField = false;
        foreach ($templateConfig['fields'] as $fieldKey => $fieldConfig) {
            if ($fieldConfig['type'] === 'email' && isset($fieldConfig['reply']) && $fieldConfig['reply'] === true) {
                $hasReplyField = true;
                break;
            }
        }
        
        if ($replyEmail !== null && file_exists($replyPath) && $hasReplyField) {
            $subject = self::getReplySubject($templateConfig);
            $replyFormSenderName = self::getReplyFormSender($templateConfig);
            $emailTemplate->addSubject($subject);

            try {
                $kirby->email([
                    'template' => $selectedTemplate . '/reply',
                    'from'     => [$formSenderEmail => $replyFormSenderName],
                    'to'       => $replyEmail,
                    'subject'  => $subject,
                    'data'     => [
                        'email' => $emailTemplate->content(),
                        'form' => $emailTemplate->form(),
                        'languageCode' => $languageCode
                    ]
                ]);
            } catch (Exception $e) {
                error_log('Error sending reply email: ' . $e->getMessage());
            }
        }
    }

    /**
     * Sets the receiver email address based on the page and data.
     *
     * @param \KirbyEmailManager\PageMethods\ContentWrapper $contentWrapper The content wrapper instance.
     * @param array $data The form data.
     * @return string The receiver email address.
     */
    public static function getToEmail($contentWrapper, $data) {
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
        
        // Check if send_to_more is enabled
        if ($contentWrapper->send_to_more()->toBool() && isset($data['topic'])) {
            // Sanitize topic to prevent email header injection
            $topic = SecurityHelper::sanitizeEmailHeader($data['topic']);
            // Subject with topic
            $subject = str_replace(':topic', $topic, $languageHelper->get('emails.mail.subject'));
        } else {
            // Standard subject
            $subject = $languageHelper->get('emails.mail.subject');
        }

        return $subject;
    }

    /**
     * Retrieves the reply-to email address based on the template configuration, data, and Kirby instance.
     *
     * @param array $templateConfig The configuration for the email template.
     * @param array $data The form data.
     * @return string|array|null The reply-to email address.
     */
    public static function getReplyToEmail($templateConfig, $data) {
        $replyToEmail = null;
        $userName = null;

        foreach ($templateConfig['fields'] as $fieldKey => $fieldConfig) {
            if (isset($fieldConfig['replyto']) && $fieldConfig['replyto'] === true && !empty($data[$fieldKey])) {
                $replyToEmail = $data[$fieldKey];
            }
            if (isset($fieldConfig['username']) && $fieldConfig['username'] === true && !empty($data[$fieldKey])) {
                // Sanitize username to prevent email header injection via display name
                $userName = SecurityHelper::sanitizeEmailHeader($data[$fieldKey]);
            }
        }

        if ($replyToEmail && $userName) {
            return [$replyToEmail => $userName];
        } elseif ($replyToEmail) {
            return $replyToEmail;
        }
        
        return null;
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
     * Retrieves the reply mail subject based on the template configuration.
     *
     * @param array $templateConfig The configuration for the email template.
     * @return string The reply mail subject.
     */
    public static function getReplySubject($templateConfig) {
        return self::initLanguageHelper($templateConfig)->get('emails.reply.subject');
    }
    
    /**
     * Retrieves the sender based on the template configuration.
     *
     * @param array $templateConfig The configuration for the email template.
     * @return string The mail sender.
     */
    public static function getFormSender($templateConfig) {
        return self::initLanguageHelper($templateConfig)->get('emails.mail.sender');
    }

    /**
     * Retrieves the reply sender based on the template configuration.
     *
     * @param array $templateConfig The configuration for the email template.
     * @return string The reply sender.
     */
    public static function getReplyFormSender($templateConfig) {
        $replySender = self::initLanguageHelper($templateConfig)->get('emails.reply.sender');
        if ($replySender === 'emails.reply.sender') {
            return kirby()->site()->title()->value();
        }
        return $replySender;
    }

    /**
     * Retrieves the reply email address based on the template configuration and data.
     *
     * @param array $templateConfig The configuration for the email template.
     * @param array $data The form data.
     * @return string|array|null The reply email address.
     */
    public static function getReplyEmail($templateConfig, $data) {
        foreach ($templateConfig['fields'] as $fieldKey => $fieldConfig) {
            if (isset($fieldConfig['replyto']) && $fieldConfig['replyto'] === true && !empty($data[$fieldKey])) {
                return $data[$fieldKey];
            }
        }
        return null;
    }
}