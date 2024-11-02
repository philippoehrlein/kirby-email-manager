<?php

namespace KirbyEmailManager\Helpers;
use Exception;

/**
 * LogHelper class for managing logging
 * 
 * This class provides methods to log errors and other messages.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class LogHelper
{
  /**
   * Logs an error message.
   * 
   * @param mixed $error The error message or exception object.
   */
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

  /**
   * Logs an info message.
   * 
   * @param string $message The info message.
   */
  public static function logInfo($message) {
    self::log('INFO', $message);
  }

  /**
   * Logs a warning message.
   * 
   * @param string $message The warning message.
   */
  public static function logWarning($message) {
      self::log('WARNING', $message);
  }

  /**
   * Logs a message with a specific level.
   * 
   * @param string $level The log level.
   * @param string $message The message to log.
   */
  private static function log($level, $message) {
      $logMessage = date('Y-m-d H:i:s') . " [$level] $message";
      error_log($logMessage);
  }
}