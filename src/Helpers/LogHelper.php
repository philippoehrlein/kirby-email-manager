<?php

namespace KirbyEmailManager\Helpers;

use Exception;

class LogHelper
{
  public static function logError($error) {
    if ($error instanceof Exception) {
        $errorDetails = [
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString()
        ];
    } else {
        $errorDetails = [
            'message' => $error,
            'file' => __FILE__,
            'line' => __LINE__,
            'trace' => (new Exception())->getTraceAsString()
        ];
    }
    
    error_log(json_encode($errorDetails));
  }

  public static function logInfo($message) {
    self::log('INFO', $message);
  }

  public static function logWarning($message) {
      self::log('WARNING', $message);
  }

  private static function log($level, $message) {
      $logMessage = date('Y-m-d H:i:s') . " [$level] $message";
      error_log($logMessage);
  }
}