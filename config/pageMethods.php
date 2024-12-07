<?php

use KirbyEmailManager\Helpers\SessionHelper;
use KirbyEmailManager\PageMethods\ContentWrapper;
use KirbyEmailManager\PageMethods\FormHandler;

return [
  'form_handler' => function ($contentWrapper = null) {
      if (!$contentWrapper) {
          $contentWrapper = new ContentWrapper($this, null);
      }
      $handler = new FormHandler(kirby(), $this, $contentWrapper);
      return $handler->handle();
  },
  'isFormSuccess' => function() {
      return SessionHelper::isFormSuccess();
  },
  'successTitle' => function() {
      return SessionHelper::getSuccessTitle($this);
  },
  'successText' => function() {
      return SessionHelper::getSuccessText($this);
  },
];