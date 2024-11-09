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
    error_log('Error: ' . $e->getMessage());
    
    $errorMessage = $languageHelper->get('error_messages.error_occurred');
    
    return [
      'type' => 'error',
      'message' => $errorMessage . $e->getMessage()
    ];
  }
}