<?php
namespace KirbyEmailManager\Helpers;

class SessionHelper
{
    private static $successMessage = null;  

    public static function isFormSuccess() {
        $session = kirby()->session();
        self::$successMessage = $session->get('form.success');

        if (self::$successMessage) {
            $session->remove('form.success');
            return true;
        }

        return false;
    }

    public static function getSuccessTitle($page) {
        return self::$successMessage['title'] ?? $page->send_to_success_title()->value();
    }

    public static function getSuccessText($page) {
        return self::$successMessage['text'] ?? $page->send_to_success_text()->value();
    }
}