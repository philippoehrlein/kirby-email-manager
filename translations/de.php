<?php

return [
    'form' => [
        'status' => [
            'ready' => 'Formular bereit zur Eingabe.',
            'invalid' => 'Formular nicht korrekt ausgefüllt.',
            'success' => 'Formular erfolgreich abgesendet.'
        ],
        'honeypot' => [
            'label' => 'Bitte nicht ausfüllen (Spamschutz)'
        ]
    ],
    'validation' => [
        'fields' => [
            'required' => 'Dieses Feld ist erforderlich.',
            'email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'name' => 'Bitte geben Sie einen gültigen Namen ein.',
            'too_short' => 'Ihre Eingabe muss mindestens :minLength Zeichen lang sein.',
            'message' => [
                'too_short' => 'Ihre Nachricht muss mindestens :minLength Zeichen lang sein.'
            ],
            'gdpr' => 'Bitte stimmen Sie den Datenschutzbestimmungen zu.',
            'date' => [
                'invalid' => 'Bitte geben Sie ein gültiges Datum ein.',
                'invalid_range' => 'Bitte geben Sie einen gültigen Datumsbereich ein.',
                'before_min' => 'Das Datum muss nach :min liegen.',
                'after_max' => 'Das Datum muss vor :max liegen.',
                'min' => 'Datum ist früher als erlaubt.',
                'max' => 'Datum ist später als erlaubt.',
                'invalid_range' => 'Enddatum muss später als das Startdatum sein.'
            ],
            'file' => [
                'too_large' => 'Die Datei ist zu groß. Maximale Größe ist :maxSize MB.',
                'invalid_type' => 'Ungültiger Dateityp. Erlaubte Typen sind: :allowedTypes.',
                'move_error' => 'Fehler beim Verschieben der Datei. PHP-Fehler: ',
                'upload_error' => 'Fehler beim Hochladen der Datei. PHP-Fehler: ',
                'too_large_ini' => 'Die Datei überschreitet die in der PHP-Konfiguration erlaubte Größe.',
                'too_large_form' => 'Die Datei überschreitet die im Formular erlaubte Größe.',
                'partial_upload' => 'Die Datei wurde nur teilweise hochgeladen.',
                'no_upload' => 'Es wurde keine Datei hochgeladen.',
                'missing_temp' => 'Der temporäre Ordner fehlt.',
                'write_error' => 'Fehler beim Speichern der Datei.',
                'upload_stopped' => 'Der Dateiupload wurde durch eine Erweiterung gestoppt.',
                'unknown_error' => 'Ein unbekannter Fehler ist beim Upload aufgetreten.',
                'malicious' => 'Die Datei enthält möglicherweise schädlichen Code.'
            ],
            'password' => [
                'min_length' => 'Das Passwort muss mindestens :minLength Zeichen lang sein.'
            ],
            'phone' => 'Bitte geben Sie eine gültige Telefonnummer ein.',
            'option' => 'Ungültige Option ausgewählt.',
            'time' => [
                'step' => 'Bitte wählen Sie eine Zeit im :interval-Minuten-Takt',
                'before_min' => 'Die Zeit muss nach :min liegen.',
                'after_max' => 'Die Zeit muss vor :max liegen.'
            ],
            'number' => [
                'too_small' => 'Die Zahl muss größer als :min sein.',
                'too_large' => 'Die Zahl muss kleiner als :max sein.',
                'invalid' => 'Bitte geben Sie eine gültige Zahl ein.'
            ],
            'url' => 'Bitte geben Sie eine gültige URL ein.'
        ],
        'system' => [
            'csrf' => 'Ungültiges CSRF-Token.',
            'submission_time' => [
                'error' => 'Ungültige Übermittlungszeit.',
                'warning' => 'Die Übermittlungszeit ist abgelaufen. Bitte überprüfen Sie Ihre Eingaben und senden Sie das Formular erneut.',
                'too_fast' => 'Das Formular wurde zu schnell übermittelt. Bitte versuchen Sie es erneut.'
            ]
        ],
        'template' => [
            'not_found' => 'Die ausgewählte E-Mail-Vorlagenkonfiguration wurde nicht gefunden.',
            'not_specified' => 'Nicht angegeben',
            'config_not_found' => 'Konfigurations-Datei nicht gefunden: ',
            'empty' => 'Die Vorlagenkonfiguration ist leer.',
            'fields_missing' => 'Der Vorlagenkonfiguration fehlt der "fields"-Schlüssel oder es ist kein Array.',
            'no_template' => 'Keine E-Mail-Vorlage ausgewählt.',
            'confirmation_not_found' => 'Bestätigungse-Mail-Vorlage nicht gefunden: ',
            'missing_key' => 'Fehlender erforderlicher Schlüssel ":key" in der Vorlagenkonfiguration.',
            'missing_property' => 'Fehlende Eigenschaft ":property" für Feld ":fieldKey" in der Vorlagenkonfiguration.',
            'validation_error' => 'Bitte füllen Sie alle erforderlichen Felder korrekt aus.',
        ]
    ],
    'buttons' => [
        'send' => [
            'label' => 'Senden'
        ],
        'reset' => [
            'label' => 'Zurücksetzen'
        ]
    ],
    'captcha' => [
        'error_messages' => [
            'missing' => 'Bitte bestätigen Sie, dass Sie ein Mensch sind.',
            'invalid' => 'Die Überprüfung ist fehlgeschlagen. Bitte versuchen Sie es erneut.'
        ]
    ],
    'panel.tab' => 'E-Mail Manager',
    'panel.templates.headline' => 'E-Mail',
    'panel.templates.label' => 'E-Mail-Vorlage',
    'panel.email.send_to_more_label' => 'E-Mail über Betreff an verschiedene E-Mail-Adressen senden?',
    'panel.email.send_to_one' => 'Nein, nur eine E-Mail-Adresse',
    'panel.email.send_to_many' => 'Ja, an mehrere E-Mail-Adressen senden',
    'panel.email.send_to_label' => 'E-Mail-Adresse',
    'panel.email.send_to_structure_label' => 'E-Mail-Adressen verwalten',
    'panel.email.send_to_structure_help' => 'Füge hier die Betreff-Optionen und die entsprechenden E-Mail-Adressen hinzu.',
    'panel.email.topic_label' => 'Betreff',
    'panel.email.topic_subject' => 'Kontaktformular Nachricht: :topic',
    'panel.email.email_label' => 'E-Mail-Adresse',
    'panel.email.subject' => 'Kontaktformular Nachricht',
    'panel.gdpr.checkbox_label' => 'Datenschutz Checkbox erforderlich?',
    'panel.gdpr.checkbox_help' => 'Wenn ausgewählt, wird die Datenschutz-Checkbox im Formular angezeigt.',
    'panel.gdpr.text_label' => 'Text für die Datenschutz-Checkbox',
    'panel.success.title_label' => 'Erfolgstitel',
    'panel.success.text_label' => 'Erfolgstext',
    'panel.success.message_label' => 'Erfolgsmeldung',
    'panel.legal.headline' => 'Rechtliches',
    'panel.legal.footer_label' => 'Email Footer',
    'panel.legal.footer_help' => 'Optionaler rechtlicher Footer, der in Bestätigungsmails angezeigt wird.',
];