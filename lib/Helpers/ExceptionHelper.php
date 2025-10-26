<?php
namespace KirbyEmailManager\Helpers;
/**
 * ExceptionHelper class provides methods to handle exceptions and generate error messages.
 * Author: Philipp Oehrlein
 * Version: 1.0.0
 */
class ExceptionHelper {
  /**
   * Handles an exception and returns an error message.
   *
   * @param \Exception $e The exception to handle.
   * @param LanguageHelper $languageHelper The language helper.
   * @return array The error message.
   */
  public static function handleException($e, LanguageHelper $languageHelper) {    
    $errorMessage = $languageHelper->get('error.error_occurred');
    
    // Only show detailed error messages in debug mode
    $detailedMessage = kirby()->option('debug', false) ? $e->getMessage() : '';
    
    return [
      'type' => 'error',
      'message' => $errorMessage . $detailedMessage
    ];
  }
}