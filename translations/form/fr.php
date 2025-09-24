<?php

return [
    'buttons' => [
        'send' => [
            'label' => 'Envoyer'
        ],
        'reset' => [
            'label' => 'Réinitialiser'
        ]
    ],
    'captcha' => [
        'error' => [
            'missing' => 'Veuillez confirmer que vous êtes humain.',
            'invalid' => 'La vérification a échoué. Veuillez réessayer.'
        ]
    ],
    'emails' => [
        'mail' => [
            'subject' => 'Formulaire de contact'
        ],
        'reply' => [
            'subject' => 'Confirmation de votre demande'
        ]
    ],
    'error' => [
        'rate_limit_exceeded' => 'Trop de requêtes. Veuillez réessayer plus tard.',
        'error_occurred' => 'Une erreur est survenue.'
    ],
    'form' => [
        'status' => [
            'ready' => 'Formulaire prêt pour la saisie.',
            'invalid' => 'Formulaire mal rempli.',
            'success' => 'Formulaire soumis avec succès.'
        ],
        'honeypot' => [
            'label' => 'Veuillez ne pas remplir (protection contre le spam)'
        ],
        'select_topic' => 'Sélectionner un sujet'
    ],
    'validation' => [
        'fields' => [
            'required' => 'Ce champ est obligatoire.',
            'email' => 'Veuillez entrer une adresse email valide.',
            'too_short' => 'Votre saisie doit comporter au moins :minLength caractères.',
            'message' => [
                'too_short' => 'Votre message doit comporter au moins :minLength caractères.',
                'too_long' => 'Votre message doit comporter au maximum :maxLength caractères.'
            ],
            'number' => 'Veuillez entrer un nombre valide.',
            'tel' => 'Veuillez entrer un numéro de téléphone valide.',
            'select' => 'Veuillez sélectionner une option valide.',
            'radio' => 'Veuillez sélectionner une option.',
            'text' => 'Veuillez entrer un texte valide.',
            'option' => 'Option sélectionnée invalide.',
            'invalid_option' => 'Option sélectionnée invalide.',
            'date' => [
                'invalid' => 'Veuillez entrer une date valide.',
                'before_min' => 'La date doit être après :min.',
                'after_max' => 'La date doit être avant :max.',
                'min' => 'La date est antérieure à la limite autorisée.',
                'max' => 'La date est postérieure à la limite autorisée.',
                'invalid_range' => 'La date de fin doit être postérieure à la date de début.'
            ],
            'date_range' => [
                'invalid' => 'Veuillez entrer une plage de dates valide.',
                'start_after_end' => 'La date de début doit être antérieure à la date de fin.'
            ],
            'file' => [
                'too_large' => 'Le fichier est trop volumineux. La taille maximale est de :maxSize Mo.',
                'invalid_type' => 'Type de fichier invalide. Types autorisés : :allowedTypes.',
                'security_error' => 'Le fichier peut contenir du code malveillant.',
                'too_many_files' => 'Le nombre maximal de fichiers est de :maxFiles.',
                'move_error' => 'Erreur lors du déplacement du fichier. Erreur PHP : ',
                'upload_error' => 'Erreur lors du téléchargement du fichier. Erreur PHP : ',
                'unknown_error' => 'Une erreur inconnue est survenue lors du téléchargement.',
                'no_file_uploaded' => 'Veuillez sélectionner une pièce jointe.',
                'security_error' => 'Le fichier a une extension non autorisée.',
                'upload_error' => 'Erreur lors du téléchargement du fichier.',
                'hidden_file' => 'Les fichiers cachés ne sont pas autorisés.',
                'invalid_signature' => 'Le type de fichier ne correspond pas à l\'extension du fichier.',
                'mime_mismatch' => 'Le fichier semble corrompu ou manipulé.'
            ],
            'password' => [
                'min_length' => 'Le mot de passe doit comporter au moins :minLength caractères.'
            ],
            'phone' => 'Veuillez entrer un numéro de téléphone valide.',
            'option' => 'Option sélectionnée invalide.',
            'time' => [
                'invalid' => 'Veuillez choisir une heure valide.',
                'step' => 'Veuillez choisir une heure par intervalle de :interval minutes.',
                'before_min' => 'L\'heure doit être après :min.',
                'after_max' => 'L\'heure doit être avant :max.'
            ],
            'number' => [
                'too_small' => 'Le nombre doit être supérieur à :min.',
                'too_large' => 'Le nombre doit être inférieur à :max.',
                'invalid' => 'Veuillez entrer un nombre valide.'
            ],
            'url' => 'Veuillez entrer une URL valide.',
            'checkbox' => 'Ce champ est obligatoire.'
        ],
        'system' => [
            'csrf' => 'Jeton CSRF invalide.',
            'gdpr_required' => 'Veuillez consentir au traitement des données.',
            'submission_time' => [
                'warning' => 'Le délai de soumission a expiré. Veuillez vérifier vos saisies et soumettre à nouveau le formulaire.',
                'too_fast' => 'Le formulaire a été soumis trop rapidement. Veuillez réessayer.'
            ]
        ],
        'template' => [
            'not_specified' => 'Non spécifié',
            'validation_error' => 'Veuillez remplir correctement tous les champs obligatoires.',
        ]
    ]
];