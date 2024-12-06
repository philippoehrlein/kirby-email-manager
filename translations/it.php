<?php

return [
    'form' => [
        'status' => [
            'ready' => 'Modulo pronto per l\'inserimento.',
            'invalid' => 'Il modulo non è stato compilato correttamente.',
            'success' => 'Modulo inviato con successo.'
        ],
        'honeypot' => [
            'label' => 'Per favore, non compilare (Protezione da spam)'
        ]
    ],
    'validation' => [
        'fields' => [
            'required' => 'Questo campo è obbligatorio.',
            'email' => 'Per favore, inserisci un indirizzo email valido.',
            'name' => 'Per favore, inserisci un nome valido.',
            'too_short' => 'Il tuo input deve contenere almeno :minLength caratteri.',
            'message' => [
                'too_short' => 'Il tuo messaggio deve contenere almeno :minLength caratteri.'
            ],
            'gdpr' => 'Per favore, accetta le condizioni di privacy.',
            'date' => [
                'invalid' => 'Per favore, inserisci una data valida.',
                'invalid_range' => 'Per favore, inserisci un intervallo di date valido.',
                'before_min' => 'La data deve essere successiva a :min.',
                'after_max' => 'La data deve essere precedente a :max.',
                'min' => 'La data è antecedente a quella consentita.',
                'max' => 'La data è successiva a quella consentita.',
                'invalid_range' => 'La data di fine deve essere successiva alla data di inizio.'
            ],
            'file' => [
                'too_large' => 'Il file è troppo grande. La dimensione massima è :maxSize MB.',
                'invalid_type' => 'Tipo di file non valido. I tipi consentiti sono: :allowedTypes.',
                'move_error' => 'Errore durante lo spostamento del file. Errore PHP: ',
                'upload_error' => 'Errore durante il caricamento del file. Errore PHP: ',
                'too_large_ini' => 'Il file supera la dimensione consentita nella configurazione PHP.',
                'too_large_form' => 'Il file supera la dimensione consentita nel modulo.',
                'partial_upload' => 'Il file è stato caricato solo parzialmente.',
                'no_upload' => 'Nessun file è stato caricato.',
                'missing_temp' => 'La cartella temporanea è mancante.',
                'write_error' => 'Errore durante il salvataggio del file.',
                'upload_stopped' => 'Il caricamento del file è stato interrotto da un\'estensione.',
                'unknown_error' => 'Si è verificato un errore sconosciuto durante il caricamento.',
                'malicious' => 'Il file potrebbe contenere codice dannoso.'
            ],
            'password' => [
                'min_length' => 'La password deve contenere almeno :minLength caratteri.'
            ],
            'phone' => 'Per favore, inserisci un numero di telefono valido.',
            'option' => 'Opzione selezionata non valida.',
            'time' => [
                'step' => 'Per favore, seleziona un orario a intervalli di :interval minuti',
                'before_min' => 'L\'orario deve essere successivo a :min.',
                'after_max' => 'L\'orario deve essere precedente a :max.'
            ],
            'number' => [
                'too_small' => 'Il numero deve essere maggiore di :min.',
                'too_large' => 'Il numero deve essere minore di :max.',
                'invalid' => 'Per favore, inserisci un numero valido.'
            ],
            'url' => 'Per favore, inserisci un URL valido.'
        ],
        'system' => [
            'csrf' => 'Token CSRF non valido.',
            'submission_time' => [
                'error' => 'Tempo di invio non valido.',
                'warning' => 'Il tempo di invio è scaduto. Per favore, controlla i tuoi dati e invia nuovamente il modulo.',
                'too_fast' => 'Il modulo è stato inviato troppo velocemente. Per favore, riprova.'
            ]
        ],
        'template' => [
            'not_found' => 'La configurazione del modello di email selezionato non è stata trovata.',
            'not_specified' => 'Non specificato',
            'config_not_found' => 'File di configurazione non trovato: ',
            'empty' => 'La configurazione del modello è vuota.',
            'fields_missing' => 'La configurazione del modello manca della chiave "fields" o non è un array.',
            'no_template' => 'Nessun modello di email selezionato.',
            'confirmation_not_found' => 'Modello di email di conferma non trovato: ',
            'missing_key' => 'Chiave richiesta ":key" mancante nella configurazione del modello.',
            'missing_property' => 'Proprietà ":property" mancante per il campo ":fieldKey" nella configurazione del modello.',
            'validation_error' => 'Bitte füllen Sie alle erforderlichen Felder korrekt aus.',
        ]
    ],
    'buttons' => [
        'send' => [
            'label' => 'Invia'
        ],
        'reset' => [
            'label' => 'Reimposta'
        ]
    ],
    'captcha' => [
        'error' => [
            'missing' => 'Per favore, verifica che sei umano.',
            'invalid' => 'La verifica ha fallito. Per favore, riprova.'
        ]
    ],
    'error' => [
        'no_template' => 'Nessun modello di email selezionato.',
        'config_file_not_found' => 'File di configurazione non trovato: ',
        'template_config_empty' => 'La configurazione del modello è vuota.',
        'error_occurred' => 'Si è verificato un errore: '
    ],
    'panel.email-manager.tab' => 'Gestore Email',
    'panel.templates.headline' => 'Email',
    'panel.templates.label' => 'Modello Email',
    'panel.email.send_to_more_label' => 'Inviare email a diversi indirizzi in base all\'oggetto?',
    'panel.email.send_to_one' => 'No, solo un indirizzo email',
    'panel.email.send_to_many' => 'Sì, invia a più indirizzi email',
    'panel.email.send_to_label' => 'Indirizzo Email',
    'panel.email.send_to_structure_label' => 'Gestisci Indirizzi Email',
    'panel.email.send_to_structure_help' => 'Aggiungi qui le opzioni dell\'oggetto e i corrispondenti indirizzi email.',
    'panel.email.topic_label' => 'Oggetto',
    'panel.email.topic_subject' => 'Messaggio dal Modulo di Contatto: :topic',
    'panel.email.email_label' => 'Indirizzo Email',
    'panel.email.subject' => 'Messaggio dal Modulo di Contatto',
    'panel.gdpr.checkbox_label' => 'Casella di controllo GDPR richiesta?',
    'panel.gdpr.checkbox_help' => 'Se selezionato, la casella di controllo GDPR verrà visualizzata nel modulo.',
    'panel.gdpr.text_label' => 'Testo per la casella di controllo GDPR',
    'panel.success.title_label' => 'Titolo di Successo',
    'panel.success.text_label' => 'Testo di Successo',
    'panel.success.message_label' => 'Messaggio di Successo',
    'panel.legal.headline' => 'Legale',
    'panel.legal.footer_label' => 'Piè di pagina Email',
    'panel.legal.footer_help' => 'Piè di pagina legale opzionale visualizzato nelle email di conferma.',
];