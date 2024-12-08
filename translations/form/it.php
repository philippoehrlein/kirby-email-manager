<?php

return [
  'form' => [
      'status' => [
          'ready' => 'Modulo pronto per l\'inserimento.',
          'invalid' => 'Il modulo non è stato compilato correttamente.',
          'success' => 'Modulo inviato con successo.'
      ],
      'honeypot' => [
          'label' => 'Si prega di non compilare (protezione antispam)'
      ],
      'select_topic' => 'Seleziona argomento'
  ],
  'validation' => [
      'fields' => [
          'required' => 'Questo campo è obbligatorio.',
          'email' => 'Si prega di inserire un indirizzo email valido.',
          'too_short' => 'Il tuo input deve essere di almeno :minLength caratteri.',
          'message' => [
              'too_short' => 'Il tuo messaggio deve essere di almeno :minLength caratteri.'
          ],
          'date' => [
              'invalid' => 'Si prega di inserire una data valida.',
              'before_min' => 'La data deve essere successiva a :min.',
              'after_max' => 'La data deve essere precedente a :max.',
              'min' => 'La data è precedente al limite consentito.',
              'max' => 'La data è successiva al limite consentito.',
              'invalid_range' => 'La data di fine deve essere successiva alla data di inizio.'
          ],
          'file' => [
              'too_large' => 'Il file è troppo grande. La dimensione massima è :maxSize MB.',
              'invalid_type' => 'Tipo di file non valido. Tipi consentiti: :allowedTypes.',
              'security_error' => 'Il file potrebbe contenere codice dannoso.',
              'too_many_files' => 'Il numero massimo di file è :maxFiles.',
              'move_error' => 'Errore durante lo spostamento del file. Errore PHP: ',
              'upload_error' => 'Errore durante il caricamento del file. Errore PHP: ',
              'unknown_error' => 'Si è verificato un errore sconosciuto durante il caricamento.',
              'no_file_uploaded' => 'Seleziona un file allegato.',
              'security_error' => 'Il file ha un\'estensione non autorizzata.',
              'upload_error' => 'Errore durante il caricamento del file.'
          ],
          'password' => [
              'min_length' => 'La password deve essere di almeno :minLength caratteri.'
          ],
          'phone' => 'Si prega di inserire un numero di telefono valido.',
          'option' => 'Opzione selezionata non valida.',
          'time' => [
              'invalid' => 'Si prega di selezionare un orario valido.',
              'step' => 'Si prega di selezionare un orario con incrementi di :interval minuti.',
              'before_min' => 'L\'orario deve essere successivo a :min.',
              'after_max' => 'L\'orario deve essere precedente a :max.'
          ],
          'number' => [
              'too_small' => 'Il numero deve essere maggiore di :min.',
              'too_large' => 'Il numero deve essere minore di :max.',
              'invalid' => 'Si prega di inserire un numero valido.'
          ],
          'url' => 'Si prega di inserire un URL valido.'
      ],
      'system' => [
          'csrf' => 'Token CSRF non valido.',
          'submission_time' => [
              'warning' => 'Il tempo per l\'invio è scaduto. Si prega di controllare i dati e inviare nuovamente il modulo.',
              'too_fast' => 'Il modulo è stato inviato troppo rapidamente. Si prega di riprovare.'
          ]
      ],
      'template' => [
          'not_specified' => 'Non specificato',
          'validation_error' => 'Si prega di compilare correttamente tutti i campi obbligatori.',
      ]
  ],
  'buttons' => [
      'send' => [
          'label' => 'Invia'
      ],
      'reset' => [
          'label' => 'Ripristina'
      ]
  ],
  'captcha' => [
      'error' => [
          'missing' => 'Si prega di confermare che sei umano.',
          'invalid' => 'La verifica è fallita. Si prega di riprovare.'
      ]
  ]
];