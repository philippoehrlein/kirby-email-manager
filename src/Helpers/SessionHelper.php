<?php
namespace KirbyEmailManager\Helpers;

/**
 * SessionHelper class for managing session-related functions
 * 
 * This class provides methods to check if a form was successfully submitted and to retrieve success messages.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class SessionHelper
{
    private static $successMessage = null;  

    /**
     * Checks if a form was successfully submitted.
     * 
     * @return bool True if the form was successfully submitted, false otherwise.
     */
    public static function isFormSuccess() {
        $session = kirby()->session();
        self::$successMessage = $session->get('form.success');

        if (self::$successMessage) {
            $session->remove('form.success');
            return true;
        }

        return false;
    }

    /**
     * Retrieves the success title from the session.
     * 
     * @param Page $page The page object.
     * @return string The success title.
     */
    public static function getSuccessTitle($page) {
        return self::$successMessage['title'] ?? $page->send_to_success_title()->value();
    }

    /**
     * Retrieves the success text from the session.
     * 
     * @param Page $page The page object.
     * @return string The success text.
     */
    public static function getSuccessText($page) {
        return self::$successMessage['text'] ?? $page->send_to_success_text()->value();
    }
}