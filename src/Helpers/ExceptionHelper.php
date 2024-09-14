<?php
namespace KirbyEmailManager\Helpers;
/**
 * ExceptionHelper class provides methods to handle exceptions and generate error messages.
 * Author: Philip Oehrlein
 * Version: 1.0.0
 */
class ExceptionHelper {
  /**
   * Handles an exception and returns an error message.
   *
   * @param \Exception $e The exception to handle.
   * @param array $translations The translations array.
   * @return array The error message.
   */
  public static function handleException($e, $translations) {
    error_log('Error: ' . $e->getMessage());
    return [
        'type' => 'error',
        'message' => $translations['error_occurred'] . (is_array($e->getMessage()) ? json_encode($e->getMessage()) : $e->getMessage())
    ];
  }
}