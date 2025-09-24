# Email Template Configuration Schema
# Version: 1.0.0

# Language handling (single vs. multilingual)
# • Single-language forms: use plain strings (scalars). Do NOT prefix with language codes.
# • Multilingual forms: use maps keyed by ISO 639-1 codes (e.g., `en`, `de`) for **every** i18n-capable field.
# • Mixing modes is invalid: a field must be either a string (single-language) or a language map (multilingual), never both.
# • Tooling guidance: If no language configuration is given, default to the language of the user's prompt/instruction.

# Basic Configuration
type: managed-template  # Required, must be 'managed-template'
name:                   # Required — string (single-language) or map (multilingual)

# Form Settings
form_submission:      # Optional, anti-spam settings
  min_time: 3         # Optional - Minimum time in seconds before form can be submitted, default: 3
  max_time: 7200      # Optional - Maximum time in seconds before form expires, default: 7200

# Email Settings
emails:               # Required
  mail:               # Required - Main email settings (formerly 'receiver')
    subject:          # Required — string (single) or map (multi) default subject
    sender:           # Required — string (single) or map (multi) sender display name (shown as email sender)
  reply:              # Optional - Reply email settings (formerly 'confirmation')
    subject:          # Required if reply is used — string (single) or map (multi)
    sender:           # Required — string (single) or map (multi) sender display name (shown as email sender)
  content:            # Optional — Custom email content blocks (string in single-language, map in multilingual)
    # These are text blocks that will be passed to the email templates
    custom_key:       # Any number of custom keys — string (single) or map (multi)

# Webhooks
webhooks:             # Optional - Webhook configurations, define only if explicitly requested
  - handler: name     # Required - Handler identifier
    events:           # Required - Array of events to trigger on
      - form.success  # Available events: form.success, form.error

# Form Fields
fields:              # Required
  fieldname:         # Any unique identifier
    type: text       # Required - Available types: text, email, tel, url, textarea, select, radio, checkbox, date, time, date-range, file, number
    label:           # Required — string (single) or map (multi)
    required: true   # Optional (default: false)
    width: 1/2       # Optional - Field width (1/1, 1/2, 1/3, 2/3, 1/4, 3/4)
    placeholder:     # Optional — string (single) or map (multi)
    validate:        # Optional - Available: email, url, phone, date, minLength:X, maxLength:Y
    error:           # Optional — string (single) or map (multi) - Custom error message
    title:           # Optional — string (single) or map (multi) - Tooltip/Help text
    aria:            # Optional — string (single) or map (multi) - Accessibility label
    help:            # Optional — string (single) or map (multi) - Help text

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
      option_key:    # Unique identifier; labels are string (single) or map (multi)

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

    #number
    min: 1           # Optional — Minimum numeric value (int/float)
    max: 999         # Optional — Maximum numeric value (int/float)
    step: 1          # Optional — Step for increments (e.g., 1 for integers)

# Validation Messages
validation:          # Optional — define only if explicitly requested or if the target language
                     # is not available in the plugin defaults (en, de, fr, es, it).
                     # If provided, these messages override the plugin defaults.
  fields:            # Optional — define only for fields present in the form
    required:        # Optional — string (single) or map (multi)
    email:           # Optional — string (single) or map (multi)
    name:            # Optional — string (single) or map (multi)
    too_short:       # Optional — string (single) or map (multi) - possible variable :minLength
    message:
      too_short:     # Optional — string (single) or map (multi) - possible variable :minLength
    gdpr:            # Optional — string (single) or map (multi)
    date:
      invalid:       # Optional — string (single) or map (multi)
      invalid_range: # Optional — string (single) or map (multi)
      before_min:    # Optional — string (single) or map (multi) - possible variable :min
      after_max:     # Optional — string (single) or map (multi) - possible variable :max
      min:           # Optional — string (single) or map (multi)
      max:           # Optional — string (single) or map (multi)
    file:
      too_large:     # Optional — string (single) or map (multi) - possible variable :maxSize
      invalid_type:  # Optional — string (single) or map (multi) - possible variable :allowedTypes
      move_error:    # Optional — string (single) or map (multi)
      upload_error:  # Optional — string (single) or map (multi)
      too_large_ini: # Optional — string (single) or map (multi)
      too_large_form:# Optional — string (single) or map (multi)
      partial_upload:# Optional — string (single) or map (multi)
      no_upload:     # Optional — string (single) or map (multi)
      missing_temp:  # Optional — string (single) or map (multi)
      write_error:   # Optional — string (single) or map (multi)
      upload_stopped:# Optional — string (single) or map (multi)
      unknown_error: # Optional — string (single) or map (multi)
      malicious:     # Optional — string (single) or map (multi)
    password:
      min_length:    # Optional — string (single) or map (multi) - possible variable :minLength
    phone:           # Optional — string (single) or map (multi)
    option:          # Optional — string (single) or map (multi)
    time:
      step:          # Optional — string (single) or map (multi) - possible variable :interval
      before_min:    # Optional — string (single) or map (multi) - possible variable :min
      after_max:     # Optional — string (single) or map (multi) - possible variable :max
    number:
      too_small:     # Optional — string (single) or map (multi) - possible variable :min
      too_large:     # Optional — string (single) or map (multi) - possible variable :max
      invalid:       # Optional — string (single) or map (multi)
    url:             # Optional — string (single) or map (multi)

# Button-Configuration
buttons:             # Optional - Form buttons
  send:
    label:          # Required — string (single) or map (multi)
  reset:
    show: false     # Optional (default: true)
    label:          # Required if show: true — string (single) or map (multi)


# If you have any questions, please ask.