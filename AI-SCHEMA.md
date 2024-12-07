# Email Template Configuration Schema
# Version: 1.0.0

# Multilingual fields should only include language prefixes (e.g., "de:", "en:") if the form is multilingual.
# For single-language forms, include values directly without language prefixes.
# If the form is multilingual, include language prefixes for all fields.

# Basis-Konfiguration
type: managed-template  # Required, must be 'managed-template'
name:                  # Required, multilingual
  en: Template Name
  de: Vorlagenname    # Add all required languages

# Formular-Einstellungen
form_submission:       # Optional, anti-spam settings
  min_time: 3         # Optional - Minimum time in seconds before form can be submitted, default: 3
  max_time: 7200      # Optional - Maximum time in seconds before form expires, default: 7200

# E-Mail-Einstellungen
emails:               # Required
  mail:               # Required - Main email settings (formerly 'receiver')
    subject:          # Required - Multilingual default subject
    sender:           # Required - Multilingual sender display name (shown as email sender)
  reply:             # Optional - Reply email settings (formerly 'confirmation')
    subject:          # Required if reply is used (multilingual)
    sender:           # Required - Multilingual sender display name (shown as email sender)
  content:            # Optional - Custom email content blocks (multilingual)
    # These are text blocks that will be passed to the email templates
    custom_key:       # Any number of custom keys, multilingual

# Webhooks
webhooks:             # Optional - Webhook configurations
  - handler: name     # Required - Handler identifier
    events:           # Required - Array of events to trigger on
      - form.success  # Available events: form.success, form.error

# Formularfelder
fields:              # Required
  fieldname:         # Any unique identifier
    type: text       # Required - Available types: text, email, tel, url, textarea, select, radio, checkbox, date, time, date-range, file
    label:           # Required (multilingual)
    required: true   # Optional (default: false)
    width: 1/2      # Optional - Field width (1/1, 1/2, 1/3, 2/3, 1/4, 3/4)
    placeholder:     # Optional (multilingual)
    validate:        # Optional - Available: email, url, phone, date, minLength:X, maxLength:Y
    error:           # Optional (multilingual) - Custom error message
    title:           # Optional (multilingual) - Tooltip/Help text
    aria:            # Optional (multilingual) - Accessibility label
    help:            # Optional (multilingual) - Help text

    # Type-specific attributes (use only relevant for chosen type):

    # text
    minlength: X      # Optional - Minimum length for text input (where X is any positive int)
    maxlength: Y      # Optional - Maximum length for text input (where Y is any positive int larger than X)
    user_name: true   # Optional - Marks field as username, will be used for reply email and reply-to address

    # email
    reply: true       # Optional - Send reply email to this address
    reply_to: true    # Optional - Use as reply-to address

    # select/radio/checkbox
    options:         # Required for these types
      option_key:    # Unique identifier
        en: Label
        de: Bezeichnung

    # time
    min: "09:00"    # Optional - 24h format
    max: "17:00"    # Optional - 24h format
    step: 1800      # Optional - Interval in seconds (1800 = 30min)

    # file
    max_files: 3    # Optional - Maximum number of files
    max_size: 5242880  # Optional - Maximum size in bytes
    allowed_mimes:   # Optional - Allowed MIME types
      - application/pdf
      - image/jpeg
      - image/png

    # date-range
    placeholder_start:  # Optional (multilingual)
    placeholder_end:    # Optional (multilingual)

    # textarea
    rows: 6          # Optional - Default height
    resizable: vertical  # Optional - Resize behavior

# Validierungsmeldungen
validation:          # Optional - Define only for fields used in the form. Do not add unused field types.
  messages:          
    fields:          # Optional
      required:      # Optional (multilingual)
      email:         # Optional (multilingual)
      name:          # Optional (multilingual)
      too_short:     # Optional (multilingual) - possible variable :minLength
      message:
        too_short:   # Optional (multilingual) - possible variable :minLength
      gdpr:          # Optional (multilingual)
      date:
        invalid:     # Optional (multilingual)
        invalid_range:  # Optional (multilingual)
        before_min:  # Optional (multilingual) - possible variable :min
        after_max:   # Optional (multilingual) - possible variable :max
        min:         # Optional (multilingual)
        max:         # Optional (multilingual)
      file:
        too_large:   # Optional (multilingual) - possible variable :maxSize
        invalid_type:  # Optional (multilingual) - possible variable :allowedTypes
        move_error:  # Optional (multilingual)
        upload_error:  # Optional (multilingual)
        too_large_ini:  # Optional (multilingual)
        too_large_form:  # Optional (multilingual)
        partial_upload:  # Optional (multilingual)
        no_upload:   # Optional (multilingual)
        missing_temp:  # Optional (multilingual)
        write_error:  # Optional (multilingual)
        upload_stopped:  # Optional (multilingual)
        unknown_error:  # Optional (multilingual)
        malicious:   # Optional (multilingual)
      password:
        min_length:  # Optional (multilingual) - possible variable :minLength
      phone:         # Optional (multilingual)
      option:        # Optional (multilingual)
      time:
        step:        # Optional (multilingual) - possible variable :interval
        before_min:  # Optional (multilingual) - possible variable :min
        after_max:   # Optional (multilingual) - possible variable :max
      number:
        too_small:   # Optional (multilingual) - possible variable :min
        too_large:   # Optional (multilingual) - possible variable :max
        invalid:     # Optional (multilingual)
      url:           # Optional (multilingual)

# Button-Konfiguration
buttons:             # Optional - Form buttons
  send:
    label:          # Required (multilingual)
  reset:
    show: false     # Optional (default: true)
    label:          # Required if show: true (multilingual)


# If you have any questions, please ask.