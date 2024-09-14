<?php

return [
    'email_tab_label' => 'Gestor de correo electrónico',
    'email_templates_headline' => 'Seleccionar plantillas de correo electrónico',
    'email_templates_label' => 'Plantilla de correo electrónico',
    'send_to_more_label' => '¿Enviar correo electrónico a diferentes direcciones según el asunto?',
    'send_to_one' => 'No, solo una dirección de correo electrónico',
    'send_to_many' => 'Sí, enviar a múltiples direcciones de correo electrónico',
    'send_to_label' => 'Dirección de correo electrónico',
    'send_to_structure_label' => 'Gestionar direcciones de correo electrónico',
    'send_to_structure_help' => 'Añade aquí las opciones de asunto y las direcciones de correo electrónico correspondientes.',
    'topic_label' => 'Asunto',
    'email_label' => 'Dirección de correo electrónico',
    'gdpr_checkbox_label' => '¿Se requiere casilla de verificación RGPD?',
    'gdpr_checkbox_help' => 'Si se selecciona, se mostrará la casilla de verificación RGPD en el formulario.',
    'gdpr_text_label' => 'Texto para la casilla de verificación RGPD',
    'form_ready' => 'Formulario listo para la entrada.',
    'field_required' => 'Este campo es obligatorio.',
    'invalid_email' => 'Por favor, introduce una dirección de correo electrónico válida.',
    'form_invalid' => 'Formulario no rellenado correctamente.',
    'form_success' => 'Formulario enviado con éxito.',
    'error_occurred' => 'Ha ocurrido un error: ',
    'not_specified' => 'No especificado',

    // Mensajes de error anidados
    'error_messages' => [
        'validation_error' => 'Por favor, rellena correctamente todos los campos obligatorios.',
        'required' => 'Este campo es obligatorio.',
        'invalid_email' => 'Por favor, introduce una dirección de correo electrónico válida.',
        'invalid_name' => 'Por favor, introduce un nombre válido.',
        'message_too_short' => 'Tu mensaje debe tener al menos :minLength caracteres.',
        'gdpr_required' => 'Por favor, acepta la política de privacidad.',
        'invalid_date' => 'Por favor, introduce una fecha válida.',
        'invalid_date_range' => 'Por favor, introduce un rango de fechas válido.',
        'date_before_min' => 'La fecha debe ser posterior a :min.',
        'date_after_max' => 'La fecha debe ser anterior a :max.',
        'file_too_large' => 'El archivo es demasiado grande. El tamaño máximo es :maxSize MB.',
        'invalid_file_type' => 'Tipo de archivo no válido. Los tipos permitidos son: :allowedTypes.',
        'min_date' => 'La fecha es anterior a la permitida.',
        'max_date' => 'La fecha es posterior a la permitida.',
        'invalid_range' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
        'date_before_min' => 'La fecha de inicio es anterior a la permitida.',
        'date_after_max' => 'La fecha de finalización es posterior a la permitida.',
        'invalid_option' => 'Opción seleccionada no válida.',
        'min_length_password' => 'La contraseña debe tener al menos :minLength caracteres.',
        'invalid_phone' => 'Por favor, introduce un número de teléfono válido.',
        'csrf_error' => 'Token CSRF no válido.',
        'submission_time_error' => 'Tiempo de envío no válido.'
    ],

    // Textos de los botones
    'button_texts' => [
        'send' => 'Enviar',
        'reset' => 'Restablecer'
    ]
];