<?php

return [
    'email_tab_label' => 'E-Mail Manager',
    'email_templates_headline' => 'E-Mail-Vorlagen auswählen',
    'email_templates_label' => 'E-Mail-Vorlage',
    'send_to_more_label' => 'E-Mail über Betreff an verschiedene E-Mail-Adressen senden?',
    'send_to_one' => 'Nein, nur eine E-Mail-Adresse',
    'send_to_many' => 'Ja, an mehrere E-Mail-Adressen senden',
    'send_to_label' => 'E-Mail-Adresse',
    'send_to_structure_label' => 'E-Mail-Adressen verwalten',
    'send_to_structure_help' => 'Füge hier die Betreff-Optionen und die entsprechenden E-Mail-Adressen hinzu.',
    'topic_label' => 'Betreff',
    'email_label' => 'E-Mail-Adresse',
    'gdpr_checkbox_label' => 'Datenschutz Checkbox erforderlich?',
    'gdpr_checkbox_help' => 'Wenn ausgewählt, wird die Datenschutz-Checkbox im Formular angezeigt.',
    'gdpr_text_label' => 'Text für die Datenschutz-Checkbox',
    'form_ready' => 'Formular bereit zur Eingabe.',
    'field_required' => 'Dieses Feld ist erforderlich.',
    'invalid_email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
    'form_invalid' => 'Formular nicht korrekt ausgefüllt.',
    'form_success' => 'Formular erfolgreich abgesendet.',
    'error_occurred' => 'Ein Fehler ist aufgetreten: ',
    'not_specified' => 'Nicht angegeben',

    // Nested error messages
    'error_messages' => [
        'validation_error' => 'Bitte füllen Sie alle erforderlichen Felder korrekt aus.',
        'required' => 'Dieses Feld ist erforderlich.',
        'invalid_email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
        'invalid_name' => 'Bitte geben Sie einen gültigen Namen ein.',
        'message_too_short' => 'Ihre Nachricht muss mindestens :minLength Zeichen lang sein.',
        'gdpr_required' => 'Bitte stimmen Sie den Datenschutzbestimmungen zu.',
        'invalid_date' => 'Bitte geben Sie ein gültiges Datum ein.',
        'invalid_date_range' => 'Bitte geben Sie einen gültigen Datumsbereich ein.',
        'date_before_min' => 'Das Datum muss nach :min liegen.',
        'date_after_max' => 'Das Datum muss vor :max liegen.',
        'file_too_large' => 'Die Datei ist zu groß. Maximale Größe ist :maxSize MB.',
        'invalid_file_type' => 'Ungültiger Dateityp. Erlaubte Typen sind: :allowedTypes.',
        'min_date' => 'Datum ist früher als erlaubt.',
        'max_date' => 'Datum ist später als erlaubt.',
        'invalid_range' => 'Enddatum muss später als das Startdatum sein.',
        'date_before_min' => 'Startdatum ist früher als erlaubt.',
        'date_after_max' => 'Enddatum ist später als erlaubt.',
        'invalid_option' => 'Ungültige Option ausgewählt.',
        'min_length_password' => 'Das Passwort muss mindestens :minLength Zeichen lang sein.',
        'invalid_phone' => 'Bitte geben Sie eine gültige Telefonnummer ein.',
        'csrf_error' => 'Ungültiges CSRF-Token.',
        'submission_time_error' => 'Ungültige Übermittlungszeit.'
    ],

    // Button texts
    'button_texts' => [
        'send' => 'Senden T',
        'reset' => 'Zurücksetzen'
    ]
];