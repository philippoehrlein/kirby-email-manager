<?php

return [
  'email_tab_label' => 'E-Mail Manager',
  'email_templates_headline' => 'Email Templates',
  'email_templates_label' => 'Email Template',
  'send_to_more_label' => 'Send email based on subject to multiple addresses?',
  'send_to_one' => 'No, only one email address',
  'send_to_many' => 'Yes, send to multiple email addresses',
  'send_to_label' => 'Email Address',
  'send_to_structure_label' => 'Manage Email Addresses',
  'send_to_structure_help' => 'Add the subject options and the corresponding email addresses here.',
  'topic_label' => 'Subject',
  'topic_subject' => 'Contact Form Message: :topic',
  'email_label' => 'Email Address',
  'gdpr_checkbox_label' => 'Is GDPR checkbox required?',
  'gdpr_checkbox_help' => 'If selected, a GDPR checkbox will be displayed in the form.',
  'gdpr_text_label' => 'Text for the GDPR checkbox',
  'form_ready' => 'Form ready for input.',
  'field_required' => 'This field is required.',
  'invalid_email' => 'Please enter a valid email address.',
  'form_invalid' => 'Form not filled out correctly.',
  'form_success' => 'Form successfully submitted.',
  'error_occurred' => 'An error occurred: ',
  'not_specified' => 'Not specified',
  'email_subject' => 'Contact Form Message',
  'send_to_success_title_label' => 'Success Title',
  'send_to_success_text_label' => 'Success Text',
  'send_to_more_success_message_label' => 'Success Message',
  'send_to_more_success_title_label' => 'Success Title',
  'send_to_more_success_text_label' => 'Success Text',
  'email_legal_headline_label' => 'Legal',

  // Nested error messages
  'error_messages' => [
      'validation_error' => 'Please fill in all required fields correctly.',
      'required' => 'This field is required.',
      'invalid_email' => 'Please enter a valid email address.',
      'invalid_name' => 'Please enter a valid name.',
      'message_too_short' => 'Your message must be at least :minLength characters long.',
      'gdpr_required' => 'Please agree to the privacy policy.',
      'invalid_date' => 'Please enter a valid date.',
      'invalid_date_range' => 'Please enter a valid date range.',
      'date_before_min' => 'The date must be after :min.',
      'date_after_max' => 'The date must be before :max.',
      'file_too_large' => 'The file is too large. Maximum size is :maxSize MB.',
      'invalid_file_type' => 'Invalid file type. Allowed types are: :allowedTypes.',
      'min_date' => 'Date is earlier than allowed.',
      'max_date' => 'Date is later than allowed.',
      'invalid_range' => 'End date must be later than the start date.',
      'date_before_min' => 'Start date is earlier than allowed.',
      'date_after_max' => 'End date is later than allowed.',
      'invalid_option' => 'Invalid option selected.',
      'min_length_password' => 'Password must be at least :minLength characters long.',
      'invalid_phone' => 'Please enter a valid phone number.',
      'csrf_error' => 'Invalid CSRF token.',
      'submission_time_error' => 'Invalid submission time.',
      'submission_time_warning' => 'The submission time is expired. Please check your input and send the form again.'
  ],

  // Button texts
  'button_texts' => [
      'send' => 'Send',
      'reset' => 'Reset'
  ]
];