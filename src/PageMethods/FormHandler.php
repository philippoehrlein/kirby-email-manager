<?php
namespace KirbyEmailManager\PageMethods;

use Kirby\Data\Data;
use Kirby\Toolkit\V;
use Kirby\Exception\Exception;

use KirbyEmailManager\Helpers\EmailHelper;
use KirbyEmailManager\Helpers\ExceptionHelper;
use KirbyEmailManager\Helpers\ValidationHelper;


return function ($kirby, $page) {
    if (class_exists('KirbyEmailManager\Helpers\ValidationHelper')) {
        echo "ValidationHelper loaded!";
    } else {
        echo "ValidationHelper NOT loaded!";
    }
    $languageCode = $kirby->language()->code() ?? 'en';
    $selectedTemplateId = $page->email_templates()->value();
    $templates = $kirby->option('philippoehrlein.kirby-email-manager.templates');
    $templateConfig = $templates[$selectedTemplateId] ?? [];

    if (empty($templateConfig)) {
        throw new Exception('Selected email template configuration not found.');
    }

    $configPath = $kirby->root('site') . '/templates/emails/' . $selectedTemplateId . '/config.yml';
    if (!file_exists($configPath)) {
        throw new Exception('Konfigurations-Datei nicht gefunden: ' . $configPath);
    }
    $templateConfig = Data::read($configPath);

    // Laden der Plugin-Übersetzungen
    $translationsPath = $kirby->root('plugins') . '/kirby-email-manager/translations/' . $languageCode . '.php';
    $translations = Data::read($translationsPath);

    $alert = [
        'type' => 'info',
        'message' => $translations['form_ready']
    ];

    if ($kirby->request()->is('POST') && get('submit')) {
        try {
            // Honeypot-Prüfung
            if (!empty(get('website'))) {
                go($page->url());
                exit;
            }

            $data = $kirby->request()->data();

            $errors = [];
            $emailContent = [];
            $senderEmail = null;
            $subject = $translations['default_subject'] ?? 'Kontaktformular';

            foreach ($templateConfig['fields'] as $fieldKey => $fieldConfig) {
                $fieldErrors = ValidationHelper::validateField($fieldKey, $fieldConfig, $data, $translations, $languageCode);
                if (!empty($fieldErrors)) {
                    $errors = array_merge($errors, $fieldErrors);
                }
            
                $emailContent[$fieldConfig['label'][$languageCode]] = is_array($data[$fieldKey]) ? implode(', ', $data[$fieldKey]) : ($data[$fieldKey] ?? $translations['not_specified']);
            }

            if ($page->gdpr_checkbox()->toBool() && empty($data['gdpr'])) {
                $errors['gdpr'] = $translations['error_messages']['gdpr_required'] ?? 'GDPR consent is required.';
            }

            if (!empty($errors)) {
                $alert['type'] = 'error';
                $alert['message'] = $translations['error_messages']['validation_error'];
                $alert['errors'] = $errors;
            } else {
                try {
                    EmailHelper::sendEmail($kirby, $page, $templateConfig, $emailContent, $data, $languageCode);
                    $alert['type'] = 'success';
                    $alert['message'] = $translations['form_success'];
                } catch (Exception $e) {
                    $alert = ExceptionHelper::handleException($e, $translations);
                }
            }
        } catch (Exception $e) {
            $alert = ExceptionHelper::handleException($e, $translations);
        }
    }

    return ['alert' => $alert, 'data' => $data ?? []];
};