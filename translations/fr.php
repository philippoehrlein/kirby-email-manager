<?php

return [
    'form' => [
        'status' => [
            'ready' => 'Formulaire prêt pour la saisie.',
            'invalid' => 'Le formulaire n\'a pas été rempli correctement.',
            'success' => 'Formulaire envoyé avec succès.'
        ],
        'honeypot' => [
            'label' => 'Veuillez ne pas remplir (Protection contre le spam)'
        ]
    ],
    'validation' => [
        'fields' => [
            'required' => 'Ce champ est obligatoire.',
            'email' => 'Veuillez saisir une adresse email valide.',
            'name' => 'Veuillez saisir un nom valide.',
            'too_short' => 'Votre entrée doit contenir au moins :minLength caractères.',
            'message' => [
                'too_short' => 'Votre message doit contenir au moins :minLength caractères.'
            ],
            'gdpr' => 'Veuillez accepter les conditions de confidentialité.',
            'date' => [
                'invalid' => 'Veuillez saisir une date valide.',
                'invalid_range' => 'Veuillez saisir une plage de dates valide.',
                'before_min' => 'La date doit être postérieure à :min.',
                'after_max' => 'La date doit être antérieure à :max.',
                'min' => 'La date est antérieure à la limite autorisée.',
                'max' => 'La date est postérieure à la limite autorisée.',
                'invalid_range' => 'La date de fin doit être postérieure à la date de début.'
            ],
            'file' => [
                'too_large' => 'Le fichier est trop volumineux. La taille maximale est de :maxSize Mo.',
                'invalid_type' => 'Type de fichier non valide. Les types autorisés sont : :allowedTypes.',
                'move_error' => 'Erreur lors du déplacement du fichier. Erreur PHP : ',
                'upload_error' => 'Erreur lors du téléchargement du fichier. Erreur PHP : ',
                'too_large_ini' => 'Le fichier dépasse la taille autorisée dans la configuration PHP.',
                'too_large_form' => 'Le fichier dépasse la taille autorisée dans le formulaire.',
                'partial_upload' => 'Le fichier n\'a été que partiellement téléchargé.',
                'no_upload' => 'Aucun fichier n\'a été téléchargé.',
                'missing_temp' => 'Le dossier temporaire est manquant.',
                'write_error' => 'Erreur lors de l\'enregistrement du fichier.',
                'upload_stopped' => 'Le téléchargement du fichier a été arrêté par une extension.',
                'unknown_error' => 'Une erreur inconnue s\'est produite lors du téléchargement.',
                'malicious' => 'Le fichier peut contenir du code malveillant.'
            ],
            'password' => [
                'min_length' => 'Le mot de passe doit contenir au moins :minLength caractères.'
            ],
            'phone' => 'Veuillez saisir un numéro de téléphone valide.',
            'option' => 'Option sélectionnée non valide.',
            'time' => [
                'step' => 'Veuillez sélectionner une heure par intervalles de :interval minutes',
                'before_min' => 'L\'heure doit être postérieure à :min.',
                'after_max' => 'L\'heure doit être antérieure à :max.'
            ],
            'number' => [
                'too_small' => 'Le nombre doit être supérieur à :min.',
                'too_large' => 'Le nombre doit être inférieur à :max.',
                'invalid' => 'Veuillez saisir un nombre valide.'
            ],
            'url' => 'Veuillez saisir une URL valide.'
        ],
        'system' => [
            'csrf' => 'Jeton CSRF non valide.',
            'submission_time' => [
                'error' => 'Heure de soumission non valide.',
                'warning' => 'Le délai de soumission a expiré. Veuillez vérifier vos entrées et soumettre à nouveau le formulaire.',
                'too_fast' => 'Le formulaire a été soumis trop rapidement. Veuillez réessayer.'
            ]
        ],
        'template' => [
            'not_found' => 'La configuration du modèle d\'email sélectionné n\'a pas été trouvée.',
            'not_specified' => 'Non spécifié',
            'config_not_found' => 'Fichier de configuration non trouvé : ',
            'empty' => 'La configuration du modèle est vide.',
            'fields_missing' => 'La configuration du modèle ne contient pas la clé "fields" ou ce n\'est pas un tableau.',
            'no_template' => 'Aucun modèle d\'email sélectionné.',
            'confirmation_not_found' => 'Modèle d\'email de confirmation non trouvé : ',
            'missing_key' => 'Clé requise ":key" manquante dans la configuration du modèle.',
            'missing_property' => 'Propriété ":property" manquante pour le champ ":fieldKey" dans la configuration du modèle.',
            'validation_error' => 'Veuillez remplir tous les champs requis correctement.'
        ]
    ],
    'buttons' => [
        'send' => [
            'label' => 'Envoyer'
        ],
        'reset' => [
            'label' => 'Réinitialiser'
        ]
    ],
    'panel.tab' => 'Gestionnaire d\'e-mails',
    'panel.templates.headline' => 'Modèles d\'e-mails',
    'panel.templates.label' => 'Modèle d\'e-mail',
    'panel.email.send_to_more_label' => 'Envoyer l\'e-mail à différentes adresses en fonction du sujet ?',
    'panel.email.send_to_one' => 'Non, une seule adresse e-mail',
    'panel.email.send_to_many' => 'Oui, envoyer à plusieurs adresses e-mail',
    'panel.email.send_to_label' => 'Adresse e-mail',
    'panel.email.send_to_structure_label' => 'Gérer les adresses e-mail',
    'panel.email.send_to_structure_help' => 'Ajoutez ici les options de sujet et les adresses e-mail correspondantes.',
    'panel.email.topic_label' => 'Sujet',
    'panel.email.topic_subject' => 'Message du formulaire de contact : :topic',
    'panel.email.email_label' => 'Adresse e-mail',
    'panel.email.subject' => 'Message du formulaire de contact',
    'panel.gdpr.checkbox_label' => 'Case à cocher RGPD requise ?',
    'panel.gdpr.checkbox_help' => 'Si sélectionné, la case à cocher RGPD sera affichée dans le formulaire.',
    'panel.gdpr.text_label' => 'Texte pour la case à cocher RGPD',
    'panel.success.title_label' => 'Titre de succès',
    'panel.success.text_label' => 'Texte de succès',
    'panel.success.message_label' => 'Message de succès',
    'panel.legal.headline' => 'Mentions légales',
    'panel.legal.footer_label' => 'Pied de page de l\'e-mail',
    'panel.legal.footer_help' => 'Pied de page légal optionnel affiché dans les e-mails de confirmation.',
];