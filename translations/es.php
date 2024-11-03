<?php

return [
    'form' => [
        'status' => [
            'ready' => 'Formulario listo para la entrada.',
            'invalid' => 'Formulario no rellenado correctamente.',
            'success' => 'Formulario enviado con éxito.'
        ],
        'honeypot' => [
            'label' => 'Por favor, no complete (Protección contra spam)'
        ]
    ],
    'validation' => [
        'fields' => [
            'required' => 'Este campo es obligatorio.',
            'email' => 'Por favor, introduzca una dirección de correo electrónico válida.',
            'name' => 'Por favor, introduzca un nombre válido.',
            'message' => [
                'too_short' => 'Su mensaje debe tener al menos :minLength caracteres.'
            ],
            'gdpr' => 'Por favor, acepte la política de protección de datos.',
            'date' => [
                'invalid' => 'Por favor, introduzca una fecha válida.',
                'invalid_range' => 'Por favor, introduzca un rango de fechas válido.',
                'before_min' => 'La fecha debe ser posterior a :min.',
                'after_max' => 'La fecha debe ser anterior a :max.',
                'min' => 'La fecha es anterior a la permitida.',
                'max' => 'La fecha es posterior a la permitida.',
                'invalid_range' => 'La fecha de finalización debe ser posterior a la fecha de inicio.'
            ],
            'file' => [
                'too_large' => 'El archivo es demasiado grande. El tamaño máximo es de :maxSize MB.',
                'invalid_type' => 'Tipo de archivo no válido. Los tipos permitidos son: :allowedTypes.',
                'move_error' => 'Error al mover el archivo. Error PHP: ',
                'upload_error' => 'Error al subir el archivo. Error PHP: ',
                'too_large_ini' => 'El archivo excede el tamaño permitido en la configuración PHP.',
                'too_large_form' => 'El archivo excede el tamaño permitido en el formulario.',
                'partial_upload' => 'El archivo solo se subió parcialmente.',
                'no_upload' => 'No se subió ningún archivo.',
                'missing_temp' => 'Falta la carpeta temporal.',
                'write_error' => 'Error al guardar el archivo.',
                'upload_stopped' => 'La subida del archivo fue detenida por una extensión.',
                'unknown_error' => 'Ocurrió un error desconocido durante la subida.',
                'malicious' => 'El archivo puede contener código malicioso.'
            ],
            'password' => [
                'min_length' => 'La contraseña debe tener al menos :minLength caracteres.'
            ],
            'phone' => 'Por favor, introduzca un número de teléfono válido.',
            'option' => 'Opción seleccionada no válida.',
            'time' => [
                'step' => 'Por favor, seleccione un tiempo en intervalos de :interval minutos',
                'before_min' => 'La hora debe ser posterior a :min.',
                'after_max' => 'La hora debe ser anterior a :max.'
            ],
            'number' => [
                'too_small' => 'El número debe ser mayor que :min.',
                'too_large' => 'El número debe ser menor que :max.',
                'invalid' => 'Por favor, introduzca un número válido.'
            ],
            'url' => 'Por favor, introduzca una URL válida.'
        ],
        'system' => [
            'csrf' => 'Token CSRF no válido.',
            'submission_time' => [
                'error' => 'Tiempo de envío no válido.',
                'warning' => 'El tiempo de envío ha expirado. Por favor, revise sus entradas y envíe el formulario nuevamente.',
                'too_fast' => 'El formulario se envió demasiado rápido. Por favor, inténtelo de nuevo.'
            ]
        ],
        'template' => [
            'not_found' => 'No se encontró la configuración de plantilla de correo electrónico seleccionada.',
            'not_specified' => 'No especificado',
            'config_not_found' => 'Archivo de configuración no encontrado: ',
            'empty' => 'La configuración de la plantilla está vacía.',
            'fields_missing' => 'Falta la clave "fields" en la configuración de la plantilla o no es un array.',
            'no_template' => 'No se seleccionó plantilla de correo electrónico.',
            'confirmation_not_found' => 'No se encontró la plantilla de correo de confirmación: ',
            'missing_key' => 'Falta la clave requerida ":key" en la configuración de la plantilla.',
            'missing_property' => 'Falta la propiedad ":property" para el campo ":fieldKey" en la configuración de la plantilla.',
            'validation_error' => 'Por favor, complete todos los campos requeridos correctamente.'
        ]
    ],
    'buttons' => [
        'send' => [
            'label' => 'Enviar'
        ],
        'reset' => [
            'label' => 'Restablecer'
        ]
    ],
    'panel.tab' => 'Gestor de Correo',
    'panel.templates.headline' => 'Plantillas de Correo',
    'panel.templates.label' => 'Plantilla de Correo',
    'panel.email.send_to_more_label' => '¿Enviar correo a diferentes direcciones según el asunto?',
    'panel.email.send_to_one' => 'No, solo una dirección de correo',
    'panel.email.send_to_many' => 'Sí, enviar a múltiples direcciones de correo',
    'panel.email.send_to_label' => 'Dirección de Correo',
    'panel.email.send_to_structure_label' => 'Gestionar Direcciones de Correo',
    'panel.email.send_to_structure_help' => 'Añade aquí las opciones de asunto y las direcciones de correo correspondientes.',
    'panel.email.topic_label' => 'Asunto',
    'panel.email.topic_subject' => 'Mensaje del Formulario de Contacto: :topic',
    'panel.email.email_label' => 'Dirección de Correo',
    'panel.email.subject' => 'Mensaje del Formulario de Contacto',
    'panel.gdpr.checkbox_label' => '¿Requiere casilla de verificación RGPD?',
    'panel.gdpr.checkbox_help' => 'Si se selecciona, se mostrará la casilla de verificación RGPD en el formulario.',
    'panel.gdpr.text_label' => 'Texto para la casilla de verificación RGPD',
    'panel.success.title_label' => 'Título de Éxito',
    'panel.success.text_label' => 'Texto de Éxito',
    'panel.success.message_label' => 'Mensaje de Éxito',
    'panel.legal.headline' => 'Legal',
    'panel.legal.footer_label' => 'Pie de Correo',
    'panel.legal.footer_help' => 'Pie legal opcional que se muestra en los correos de confirmación.',
];