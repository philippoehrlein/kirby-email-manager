<?php
namespace KirbyEmailManager\Helpers;
class ExceptionHelper {
  public static function handleException($e, $translations) {
    error_log('Error: ' . $e->getMessage());
    return [
        'type' => 'error',
        'message' => $translations['error_occurred'] . (is_array($e->getMessage()) ? json_encode($e->getMessage()) : $e->getMessage())
    ];
  }
}