<?php

return [
    'buttons' => [
        'send' => [
            'label' => 'Enviar'
        ],
        'reset' => [
            'label' => 'Restablecer'
        ]
    ],
    'captcha' => [
        'error' => [
            'missing' => 'Por favor, confirma que eres humano.',
            'invalid' => 'La verificación falló. Por favor, inténtalo de nuevo.'
        ]
    ],
    'emails' => [
        'mail' => [
            'subject' => 'Formulario de contacto'
        ],
        'reply' => [
            'subject' => 'Confirmación de su consulta'
        ]
    ],
    'error' => [
        'rate_limit_exceeded' => 'Demasiadas solicitudes. Por favor, inténtalo de nuevo más tarde.'
    ],
    'form' => [
        'status' => [
            'ready' => 'Formulario listo para su uso.',
            'invalid' => 'El formulario no está correctamente completado.',
            'success' => 'Formulario enviado con éxito.'
        ],
        'honeypot' => [
            'label' => 'Por favor, no rellenar (protección contra spam)'
        ],
        'select_topic' => 'Seleccionar tema'
    ],
    'validation' => [
        'fields' => [
            'required' => 'Este campo es obligatorio.',
            'email' => 'Por favor, introduce una dirección de correo válida.',
            'too_short' => 'Tu entrada debe tener al menos :minLength caracteres.',
            'message' => [
                'too_short' => 'Tu mensaje debe tener al menos :minLength caracteres.'
            ],
            'date' => [
                'invalid' => 'Por favor, introduce una fecha válida.',
                'before_min' => 'La fecha debe ser posterior a :min.',
                'after_max' => 'La fecha debe ser anterior a :max.',
                'min' => 'La fecha es anterior al límite permitido.',
                'max' => 'La fecha es posterior al límite permitido.',
                'invalid_range' => 'La fecha de finalización debe ser posterior a la fecha de inicio.'
            ],
            'file' => [
                'too_large' => 'El archivo es demasiado grande. El tamaño máximo es :maxSize MB.',
                'invalid_type' => 'Tipo de archivo no válido. Tipos permitidos: :allowedTypes.',
                'security_error' => 'El archivo puede contener código dañino.',
                'too_many_files' => 'El número máximo de archivos es :maxFiles.',
                'move_error' => 'Error al mover el archivo. Error de PHP: ',
                'upload_error' => 'Error al subir el archivo. Error de PHP: ',
                'unknown_error' => 'Se produjo un error desconocido durante la carga.',
                'no_file_uploaded' => 'Por favor, selecciona un archivo adjunto.',
                'security_error' => 'El archivo tiene una extensión no permitida.',
                'upload_error' => 'Error al subir el archivo.',
                'hidden_file' => 'Los archivos ocultos no están permitidos.',
                'invalid_signature' => 'El tipo de archivo no coincide con la extensión del archivo.',
                'mime_mismatch' => 'El archivo parece estar dañado o manipulado.'
            ],
            'password' => [
                'min_length' => 'La contraseña debe tener al menos :minLength caracteres.'
            ],
            'phone' => 'Por favor, introduce un número de teléfono válido.',
            'option' => 'Opción seleccionada no válida.',
            'time' => [
                'invalid' => 'Por favor, selecciona un tiempo válido.',
                'step' => 'Por favor, selecciona un tiempo en intervalos de :interval minutos.',
                'before_min' => 'El tiempo debe ser posterior a :min.',
                'after_max' => 'El tiempo debe ser anterior a :max.'
            ],
            'number' => [
                'too_small' => 'El número debe ser mayor que :min.',
                'too_large' => 'El número debe ser menor que :max.',
                'invalid' => 'Por favor, introduce un número válido.'
            ],
            'url' => 'Por favor, introduce una URL válida.'
        ],
        'system' => [
            'csrf' => 'Token CSRF no válido.',
            'submission_time' => [
                'warning' => 'El tiempo de envío ha expirado. Por favor, revisa tus entradas y envía el formulario nuevamente.',
                'too_fast' => 'El formulario se envió demasiado rápido. Por favor, inténtalo de nuevo.'
            ]
        ],
        'template' => [
            'not_specified' => 'No especificado',
            'validation_error' => 'Por favor, completa correctamente todos los campos obligatorios.',
        ]
    ]
];