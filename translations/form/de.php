<?php

return [
    'buttons' => [
        'send' => [
            'label' => 'Senden'
        ],
        'reset' => [
            'label' => 'Zurücksetzen'
        ]
    ],
    'captcha' => [
        'error' => [
            'missing' => 'Bitte bestätigen Sie, dass Sie ein Mensch sind.',
            'invalid' => 'Die Überprüfung ist fehlgeschlagen. Bitte versuchen Sie es erneut.'
        ]
    ],
    'emails' => [
        'mail' => [
            'subject' => 'Kontaktformular Nachricht'
        ],
        'reply' => [
            'subject' => 'Bestätigung Ihrer Anfrage'
        ]
    ],
    'error' => [
        'rate_limit_exceeded' => 'Zu viele Anfragen. Bitte versuche es später erneut.'
    ],
    'form' => [
        'status' => [
            'ready' => 'Formular bereit zur Eingabe.',
            'invalid' => 'Formular nicht korrekt ausgefüllt.',
            'success' => 'Formular erfolgreich abgesendet.'
        ],
        'honeypot' => [
            'label' => 'Bitte nicht ausfüllen (Spamschutz)'
        ],
        'select_topic' => 'Thema auswählen'
    ],
    'validation' => [
        'fields' => [
            'required' => 'Dieses Feld ist erforderlich.',
            'email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'too_short' => 'Ihre Eingabe muss mindestens :minLength Zeichen lang sein.',
            'message' => [
                'too_short' => 'Ihre Nachricht muss mindestens :minLength Zeichen lang sein.'
            ],
            'date' => [
                'invalid' => 'Bitte geben Sie ein gültiges Datum ein.',
                'before_min' => 'Das Datum muss nach :min liegen.',
                'after_max' => 'Das Datum muss vor :max liegen.',
                'min' => 'Datum ist früher als erlaubt.',
                'max' => 'Datum ist später als erlaubt.',
                'invalid_range' => 'Enddatum muss später als das Startdatum sein.'
            ],
            'file' => [
                'too_large' => 'Die Datei ist zu groß. Maximale Größe ist :maxSize MB.',
                'invalid_type' => 'Ungültiger Dateityp. Erlaubte Typen sind: :allowedTypes.',
                'security_error' => 'Die Datei enthält möglicherweise schädlichen Code.',
                'too_many_files' => 'Die maximale Anzahl der Dateien beträgt :maxFiles.',
                'move_error' => 'Fehler beim Verschieben der Datei. PHP-Fehler: ',
                'upload_error' => 'Fehler beim Hochladen der Datei. PHP-Fehler: ',
                'unknown_error' => 'Ein unbekannter Fehler ist beim Upload aufgetreten.',
                'no_file_uploaded' => 'Wählen Sie einen Anhang aus.',
                'security_error' => 'Die Datei hat eine unerlaubte Dateiendung.',
                'upload_error' => 'Fehler beim Hochladen der Datei.',
                'hidden_file' => 'Versteckte Dateien sind nicht erlaubt.',
                'invalid_signature' => 'Dateityp stimmt nicht mit der Dateiendung überein.',
                'mime_mismatch' => 'Die Datei scheint beschädigt oder manipuliert zu sein.'
            ],
            'password' => [
                'min_length' => 'Das Passwort muss mindestens :minLength Zeichen lang sein.'
            ],
            'phone' => 'Bitte geben Sie eine gültige Telefonnummer ein.',
            'option' => 'Ungültige Option ausgewählt.',
            'time' => [
                'invalid' => 'Bitte geben Sie eine gültige Uhrzeit ein.',
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
                'warning' => 'Die Übermittlungszeit ist abgelaufen. Bitte überprüfen Sie Ihre Eingaben und senden Sie das Formular erneut.',
                'too_fast' => 'Das Formular wurde zu schnell übermittelt. Bitte versuchen Sie es erneut.'
            ]
        ],
        'template' => [
            'not_specified' => 'Nicht angegeben',
            'validation_error' => 'Bitte füllen Sie alle erforderlichen Felder korrekt aus.',
        ]
    ]
];
