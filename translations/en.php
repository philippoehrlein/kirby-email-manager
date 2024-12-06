<?php

return [
    'form' => [
        'status' => [
            'ready' => 'Form ready for input.',
            'invalid' => 'Form not filled out correctly.',
            'success' => 'Form successfully submitted.'
        ],
        'honeypot' => [
            'label' => 'Please do not fill in (Spam protection)'
        ]
    ],
    'validation' => [
        'fields' => [
            'required' => 'This field is required.',
            'email' => 'Please enter a valid email address.',
            'name' => 'Please enter a valid name.',
            'too_short' => 'Your input must be at least :minLength characters long.',
            'message' => [
                'too_short' => 'Your message must be at least :minLength characters long.'
            ],
            'gdpr' => 'Please agree to the privacy policy.',
            'date' => [
                'invalid' => 'Please enter a valid date.',
                'invalid_range' => 'Please enter a valid date range.',
                'before_min' => 'The date must be after :min.',
                'after_max' => 'The date must be before :max.',
                'min' => 'Date is earlier than allowed.',
                'max' => 'Date is later than allowed.',
                'invalid_range' => 'End date must be later than start date.'
            ],
            'file' => [
                'too_large' => 'The file is too large. Maximum size is :maxSize MB.',
                'invalid_type' => 'Invalid file type. Allowed types are: :allowedTypes.',
                'security_error' => 'The file may contain malicious code.',
                'too_many_files' => 'The maximum number of files is :maxFiles.',
                'move_error' => 'Error moving the file. PHP error: ',
                'upload_error' => 'Error uploading the file. PHP error: ',
                'unknown_error' => 'An unknown error occurred during upload.',
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
                'step' => 'Please select a time in :interval minute intervals',
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
                'error' => 'Invalid submission time.',
                'warning' => 'The submission time has expired. Please check your inputs and submit the form again.',
                'too_fast' => 'The form was submitted too quickly. Please try again.'
            ]
        ],
        'template' => [
            'not_found' => 'The selected email template configuration was not found.',
            'not_specified' => 'Not specified',
            'config_not_found' => 'Configuration file not found: ',
            'empty' => 'The template configuration is empty.',
            'fields_missing' => 'The template configuration is missing the "fields" key or it is not an array.',
            'no_template' => 'No email template selected.',
            'confirmation_not_found' => 'Confirmation email template not found: ',
            'missing_key' => 'Missing required key ":key" in template configuration.',
            'missing_property' => 'Missing property ":property" for field ":fieldKey" in template configuration.',
            'validation_error' => 'Please fill in all required fields correctly.'
        ]
    ],
    'buttons' => [
        'send' => [
            'label' => 'Send!!!'
        ],
        'reset' => [
            'label' => 'Reset'
        ]
    ],
    'captcha' => [
        'error' => [
            'missing' => 'Please verify that you are human.',
            'invalid' => 'Verification failed. Please try again.'
        ]
    ],
    'error' => [
        'no_template' => 'No email template selected.',
        'config_file_not_found' => 'Configuration file not found: ',
        'template_config_empty' => 'The template configuration is empty.',
        'error_occurred' => 'An error occurred: '
    ],
    'panel.email-manager.tab' => 'Email Manager',
    'panel.templates.headline' => 'Email',
    'panel.templates.label' => 'Email Template',
    'panel.email.send_to_more_label' => 'Send email to different email addresses based on subject?',
    'panel.email.send_to_one' => 'No, only one email address',
    'panel.email.send_to_many' => 'Yes, send to multiple email addresses',
    'panel.email.send_to_label' => 'Email Address',
    'panel.email.send_to_structure_label' => 'Manage Email Addresses',
    'panel.email.send_to_structure_help' => 'Add subject options and corresponding email addresses here.',
    'panel.email.topic_label' => 'Subject',
    'panel.email.topic_subject' => 'Contact Form Message: :topic',
    'panel.email.email_label' => 'Email Address',
    'panel.email.subject' => 'Contact Form Message',
    'panel.gdpr.checkbox_label' => 'GDPR Checkbox Required?',
    'panel.gdpr.checkbox_help' => 'If selected, the GDPR checkbox will be displayed in the form.',
    'panel.gdpr.text_label' => 'Text for GDPR Checkbox',
    'panel.success.title_label' => 'Success Title',
    'panel.success.text_label' => 'Success Text',
    'panel.success.message_label' => 'Success Message',
    'panel.legal.headline' => 'Legal',
    'panel.legal.footer_label' => 'Email Footer',
    'panel.legal.footer_help' => 'Optional legal footer displayed in confirmation emails.',
];