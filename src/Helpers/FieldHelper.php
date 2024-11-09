<?php

namespace KirbyEmailManager\Helpers;

use KirbyEmailManager\Helpers\FormHelper;
use KirbyEmailManager\Helpers\FieldAttributeHelper;
use Kirby\Toolkit\Html;

/**
 * Field class for managing form fields
 * 
 * This class provides methods to create and manage form fields.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class Field {
    private $name;
    private $config;
    private $fieldValue;
    public $required;
    public $class;
    public $options;
    public $placeholder;
    public $title;
    public $ariaLabel;
    public $type;
    private $attributes = [];
    
    /**
     * Constructor for the Field class
     * 
     * @param string $name The name of the field.
     * @param array $config The configuration for the field.
     */
    public function __construct($name, $config = []) {
        $this->name = $name;
        $this->config = $config;
    }
    
    /**
     * Retrieves the name of the field.
     * 
     * @return string The name of the field.
     */
    public function name() {
        return $this->name;
    }

    /**
     * Retrieves the value of the field.
     * 
     * @param string $key The key of the value to retrieve.
     * @return mixed The value of the field.
     */
    public function value($key = null) {
        if ($key === null) {
            return $this->fieldValue;
        }
        
        if (!is_array($this->fieldValue)) {
            return '';
        }
        
        return $this->fieldValue[$key] ?? '';
    }

    /**
     * Sets the value of the field.
     * 
     * @param mixed $value The value to set.
     * @return object The current field object.
     */
    public function setValue($value) {
        $this->fieldValue = $value;
        return $this;
    }

    /**
     * Sets the value of the field.
     * 
     * @param string $name The name of the attribute to set.
     * @param mixed $value The value to set.
     */
    public function __set($name, $value) {
        if ($name === 'value') {
            $this->fieldValue = $value;
            return; 
        } else {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * Retrieves the value of a specific attribute.
     * 
     * @param string $name The name of the attribute to retrieve.
     * @return mixed The value of the attribute.
     */
    public function __get($name) {
        if ($name === 'value') {
            return $this->fieldValue;
        }
        return $this->attributes[$name] ?? null;
    }

    /**
     * Retrieves the attributes of the field.
     * 
     * @param string $key The key of the attribute to retrieve.
     * @return string The attributes of the field.
     */
    public function attr($key = null) {
        $attrs = [
            'type' => $this->type,
            'id' => $this->name,
            'name' => $this->name,
            'class' => $this->class,
            'value' => $this->value(),
            'required' => $this->required,
            'placeholder' => $this->placeholder,
            'title' => $this->title,
            'aria-label' => $this->ariaLabel
        ];

        if($key !== null) {
            $attrs = $this->attributes[$key];
        }else {    
            $attrs = array_merge($attrs, $this->attributes);    
        }
        
        return Html::attr(array_filter($attrs, function($value) {
            return $value !== null && $value !== false && $value !== '';
        }));
    }

    public function className() {
        return FieldHelper::getFieldClassName('daterange-wrapper', $this->config);
    }
}

/**
 * FieldHelper class for managing form fields
 * 
 * This class provides methods to get the class name and options for form fields.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class FieldHelper
{
    /**
     * Retrieves the class name for a form field.
     * 
     * @param string $element The element to get the class name for.
     * @param array $fieldConfig The configuration for the field.
     * @param string $modifier The modifier for the class name.
     * @return string The class name for the field.
     */
    public static function getFieldClassName(string $element, array $fieldConfig, ?string $modifier = null): string
    {
        return FormHelper::getClassName($element, $fieldConfig, $modifier);
    }

    /**
     * Retrieves the options for a form field.
     * 
     * @param array $fieldConfig The configuration for the field.
     * @param string $languageCode The language code.
     * @return array The options for the field.
     */
    public static function getOptions(array $fieldConfig, ?string $languageCode = null): array
    {
        $options = [];
        foreach ($fieldConfig['options'] as $optionKey => $optionLabel) {
            $label = is_array($optionLabel) ? ($optionLabel[$languageCode] ?? $optionKey) : $optionLabel;
            $options[$optionKey] = $label;
        }
        return $options;
    }

    /**
     * Prepares the attributes for a form field.
     * 
     * @param array $fieldConfig The configuration for the field.
     * @param string $fieldKey The key of the field.
     * @param string $inputClass The class for the input element.
     * @param mixed $value The value of the field.
     * @param string $placeholder The placeholder for the field.
     * @param string $languageCode The language code.
     * @param array $commonAttributes Additional common attributes.
     * @return object The prepared field object.
     */
    public static function prepareFieldAttributes(
        array $fieldConfig,
        string $fieldKey,
        string $inputClass,
        $value,
        $placeholder,
        $languageCode,
        array $commonAttributes = []
    ): object {
        $field = new Field($fieldKey);
        
        $baseAttributes = FieldAttributeHelper::getBaseAttributes(
            $fieldKey,
            $fieldConfig,
            $inputClass,
            $commonAttributes
        );
        
        $fieldAttributes = FieldAttributeHelper::getFieldAttributes(
            $fieldConfig['type'],
            $baseAttributes,
            $fieldConfig,
            $value,
            $placeholder
        );
        
        foreach ($fieldAttributes as $key => $val) {
            $field->$key = $val;
        }

        if (in_array($fieldConfig['type'], ['checkbox', 'radio', 'select'])) {
            $field->options = self::getOptions($fieldConfig, $languageCode);
        }
        
        return $field;
    }

    /**
     * Retrieves the value of a form field from the data array.
     * 
     * @param array $data The data array.
     * @param string $fieldKey The key of the field.
     * @return mixed The value of the field.
     */
    public static function getValue(array $data, string $fieldKey)
    {
        return $data[$fieldKey] ?? null;
    }
}