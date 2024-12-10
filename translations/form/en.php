<?php

return [
    'buttons' => [
        'send' => [
            'label' => 'Send'
        ],
        'reset' => [
            'label' => 'Reset'
        ]
    ],
    'captcha' => [
        'error' => [
            'missing' => 'Please confirm that you are human.',
            'invalid' => 'Verification failed. Please try again.'
        ]
    ],
    'emails' => [
        'mail' => [
            'subject' => 'Contact Form Message'
        ],
        'reply' => [
            'subject' => 'Confirmation of your inquiry'
        ]
    ],
    'error' => [
        'rate_limit_exceeded' => 'Too many requests. Please try again later.'
    ],
    'form' => [
        'status' => [
            'ready' => 'Form ready for input.',
            'invalid' => 'Form not filled out correctly.',
            'success' => 'Form submitted successfully.'
        ],
        'honeypot' => [
            'label' => 'Please do not fill in (Spam protection)'
        ],
        'select_topic' => 'Select topic'
    ],
    'validation' => [
        'fields' => [
            'required' => 'This field is required.',
            'email' => 'Please enter a valid email address.',
            'too_short' => 'Your input must be at least :minLength characters long.',
            'message' => [
                'too_short' => 'Your message must be at least :minLength characters long.'
            ],
            'date' => [
                'invalid' => 'Please enter a valid date.',
                'before_min' => 'The date must be after :min.',
                'after_max' => 'The date must be before :max.',
                'min' => 'Date is earlier than allowed.',
                'max' => 'Date is later than allowed.',
                'invalid_range' => 'End date must be later than the start date.'
            ],
            'file' => [
                'too_large' => 'The file is too large. Maximum size is :maxSize MB.',
                'invalid_type' => 'Invalid file type. Allowed types are: :allowedTypes.',
                'security_error' => 'The file may contain harmful code.',
                'too_many_files' => 'The maximum number of files is :maxFiles.',
                'move_error' => 'Error moving the file. PHP error: ',
                'upload_error' => 'Error uploading the file. PHP error: ',
                'unknown_error' => 'An unknown error occurred during the upload.',
                'no_file_uploaded' => 'Please select an attachment.',
                'security_error' => 'The file has an unauthorized file extension.',
                'upload_error' => 'Error uploading the file.'
            ],
            'password' => [
                'min_length' => 'The password must be at least :minLength characters long.'
            ],
            'phone' => 'Please enter a valid phone number.',
            'option' => 'Invalid option selected.',
            'time' => [
                'invalid' => 'Please select a valid time.',
                'step' => 'Please select a time in :interval-minute increments.',
                'before_min' => 'The time must be after :min.',
                'after_max' => 'The time must be before :max.'
            ],
            'number' => [
                'too_small' => 'The number must be greater than :min.',
                'too_large' => 'The number must be less than :max.',
                'invalid' => 'Please enter a valid number.'
            ],
            'url' => 'Please enter a valid URL.'
        ],
        'system' => [
            'csrf' => 'Invalid CSRF token.',
            'submission_time' => [
                'warning' => 'The submission time has expired. Please check your inputs and resubmit the form.',
                'too_fast' => 'The form was submitted too quickly. Please try again.'
            ]
        ],
        'template' => [
            'not_specified' => 'Not specified',
            'validation_error' => 'Please fill out all required fields correctly.',
        ]
    ]
];