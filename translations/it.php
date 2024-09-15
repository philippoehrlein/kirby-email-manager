<?php

return [
    'email_tab_label' => 'Gestore delle email',
    'email_templates_headline' => 'Seleziona un modello di email',
    'email_templates_label' => 'Modello di email',
    'send_to_more_label' => 'Inviare email a indirizzi diversi in base all\'oggetto?',
    'send_to_one' => 'No, solo un indirizzo email',
    'send_to_many' => 'Sì, invia a più indirizzi email',
    'send_to_label' => 'Indirizzo email',
    'send_to_structure_label' => 'Gestisci gli indirizzi email',
    'send_to_structure_help' => 'Aggiungi qui le opzioni per l\'oggetto e gli indirizzi email corrispondenti.',
    'topic_label' => 'Oggetto',
    'email_label' => 'Indirizzo email',
    'gdpr_checkbox_label' => 'Casella di controllo della privacy obbligatoria?',
    'gdpr_checkbox_help' => 'Se selezionato, la casella di controllo della privacy apparirà nel modulo.',
    'gdpr_text_label' => 'Testo per la casella di controllo della privacy',
    'form_ready' => 'Modulo pronto per l\'inserimento.',
    'field_required' => 'Questo campo è obbligatorio.',
    'invalid_email' => 'Per favore, inserisci un indirizzo email valido.',
    'form_invalid' => 'Il modulo non è stato compilato correttamente.',
    'form_success' => 'Modulo inviato con successo.',
    'error_occurred' => 'Si è verificato un errore: ',
    'not_specified' => 'Non specificato',

    // Messaggi di errore annidati
    'error_messages' => [
        'validation_error' => 'Per favore, compila correttamente tutti i campi obbligatori.',
        'required' => 'Questo campo è obbligatorio.',
        'invalid_email' => 'Per favore, inserisci un indirizzo email valido.',
        'invalid_name' => 'Per favore, inserisci un nome valido.',
        'message_too_short' => 'Il tuo messaggio deve contenere almeno :minLength caratteri.',
        'gdpr_required' => 'Per favore, accetta le condizioni di privacy.',
        'invalid_date' => 'Per favore, inserisci una data valida.',
        'invalid_date_range' => 'Per favore, inserisci un intervallo di date valido.',
        'date_before_min' => 'La data deve essere successiva a :min.',
        'date_after_max' => 'La data deve essere precedente a :max.',
        'file_too_large' => 'Il file è troppo grande. La dimensione massima è :maxSize MB.',
        'invalid_file_type' => 'Tipo di file non valido. I tipi consentiti sono: :allowedTypes.',
        'min_date' => 'La data è antecedente a quella consentita.',
        'max_date' => 'La data è successiva a quella consentita.',
        'invalid_range' => 'La data di fine deve essere successiva alla data di inizio.',
        'date_before_min' => 'La data di inizio è antecedente a quella consentita.',
        'date_after_max' => 'La data di fine è successiva a quella consentita.',
        'invalid_option' => 'Opzione selezionata non valida.',
        'min_length_password' => 'La password deve contenere almeno :minLength caratteri.',
        'invalid_phone' => 'Per favore, inserisci un numero di telefono valido.',
        'csrf_error' => 'Token CSRF non valido.',
        'submission_time_error' => 'Tempo di invio non valido.',
        'submission_time_warning' => 'Il tempo di invio è scaduto. Per favore, controlla i tuoi input e invia il modulo di nuovo.'
    ],

    // Testi dei pulsanti
    'button_texts' => [
        'send' => 'Invia',
        'reset' => 'Resetta'
    ]
];