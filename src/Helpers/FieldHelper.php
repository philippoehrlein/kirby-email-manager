<?php

namespace KirbyEmailManager\Helpers;

use KirbyEmailManager\Helpers\FormHelper;
use KirbyEmailManager\Helpers\FieldAttributeHelper;
use Kirby\Toolkit\Html;

class Field {
    private $name;
    private $config;
    private $fieldValue;
    public $required;
    public $class;
    public $options;
    public $placeholder;
    public $type;
    private $attributes = [];
    
    public function __construct($name, $config = []) {
        $this->name = $name;
        $this->config = $config;
    }
    
    public function name() {
        return $this->name;
    }

    public function value($key = null) {
        if ($key === null) {
            return $this->fieldValue;
        }
        
        if (!is_array($this->fieldValue)) {
            return '';
        }
        
        return $this->fieldValue[$key] ?? '';
    }

    public function setValue($value) {
        $this->fieldValue = $value;
        return $this;
    }

    public function __set($name, $value) {
        if ($name === 'value') {
            $this->fieldValue = $value;
            return;
        }
        $this->attributes[$name] = $value;
    }

    public function __get($name) {
        if ($name === 'value') {
            return $this->fieldValue;
        }
        return $this->attributes[$name] ?? null;
    }

    public function attr() {
        $attrs = [
            'type' => $this->type,
            'id' => $this->name,
            'name' => $this->name,
            'class' => $this->class,
            'value' => $this->value(),
            'required' => $this->required,
            'placeholder' => $this->placeholder,
            'title' => $this->title,
            'aria-label' => $this->{'aria-label'}
        ];

        $attrs = array_merge($attrs, $this->attributes);
        
        return Html::attr(array_filter($attrs, function($value) {
            return $value !== null && $value !== false && $value !== '';
        }));
    }

    public function className() {
        return FieldHelper::getFieldClassName('daterange-wrapper', $this->config);
    }
}

class FieldHelper
{
    public static function getFieldClassName(string $element, array $fieldConfig, ?string $modifier = null): string
    {
        return FormHelper::getClassName($element, $fieldConfig, $modifier);
    }

    public static function getOptions(array $fieldConfig, ?string $languageCode = null): array
    {
        $options = [];
        foreach ($fieldConfig['options'] as $optionKey => $optionLabel) {
            $label = is_array($optionLabel) ? ($optionLabel[$languageCode] ?? $optionKey) : $optionLabel;
            $options[$optionKey] = $label;
        }
        return $options;
    }

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
        
        // Basis-Attribute holen
        $baseAttributes = FieldAttributeHelper::getBaseAttributes(
            $fieldKey,
            $fieldConfig,
            $inputClass,
            $commonAttributes
        );
        
        // Feld-spezifische Attribute holen
        $fieldAttributes = FieldAttributeHelper::getFieldAttributes(
            $fieldConfig['type'],
            $baseAttributes,
            $fieldConfig,
            $value,
            $placeholder,
            $languageCode
        );
        
        // Attribute dem Field-Objekt zuweisen
        foreach ($fieldAttributes as $key => $val) {
            $field->$key = $val;
        }
        
        // Options für bestimmte Feldtypen setzen
        if (in_array($fieldConfig['type'], ['checkbox', 'radio', 'select'])) {
            $field->options = self::getOptions($fieldConfig, $languageCode);
        }
        
        return $field;
    }

    public static function getValue(array $data, string $fieldKey)
    {
        return $data[$fieldKey] ?? null;
    }
}