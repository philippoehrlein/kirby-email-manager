<?php

namespace KirbyEmailManager\Helpers;

/**
 * UrlHelper class for managing URL-related functions
 * 
 * This class provides methods to convert relative URLs to absolute URLs.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class UrlHelper
{
    /**
     * Converts relative URLs to absolute URLs in the content.
     * 
     * @param string $content The content to convert.
     * @param Kirby $kirby The Kirby instance.
     * @return string The converted content.
     */
    public static function convertLinksToAbsolute($content, $kirby)
    {
        return preg_replace_callback(
            '/<a\s+(?:[^>]*?\s+)?href="([^"]*)"/',
            function ($matches) use ($kirby) {
                $url = $matches[1];
                $convertedUrl = self::convertUrl($url, $kirby);
                return str_replace($url, $convertedUrl, $matches[0]);
            },
            $content
        );
    }

    /**
     * Converts a URL to an absolute URL.
     * 
     * @param string $url The URL to convert.
     * @param Kirby $kirby The Kirby instance.
     * @return string The converted URL.
     */
    private static function convertUrl($url, $kirby)
    {
        if (strpos($url, 'x-webdoc://') === 0) {
            return self::convertXWebdocUrl($url, $kirby);
        } elseif (strpos($url, '/@/page/') === 0) {
            return self::convertInternalPageUrl($url, $kirby);
        } elseif (!self::isAbsoluteUrl($url) && !self::isSpecialUrl($url)) {
            return self::convertRelativeUrl($url);
        }
        return $url;
    }

    /**
     * Converts an x-webdoc URL to an absolute URL.
     * 
     * @param string $url The URL to convert.
     * @param Kirby $kirby The Kirby instance.
     * @return string The converted URL.
     */
    private static function convertXWebdocUrl($url, $kirby)
    {
        $pagePos = strpos($url, '/@/page/');
        if ($pagePos !== false) {
            $pageId = substr($url, $pagePos + 8);
            $page = $kirby->site()->page('@' . $pageId);
            if ($page) {
                $url = $page->url();
                return $url;
            } else {
                return $url;
            }
        } else {
            $url = preg_replace('/^x-webdoc:\/\/[^\/]+/', '', $url);
            $url = self::getBaseUrl() . '/' . ltrim($url, '/');
            return $url;
        }
    }

    /**
     * Converts an internal page URL to an absolute URL.
     * 
     * @param string $url The URL to convert.
     * @param Kirby $kirby The Kirby instance.
     * @return string The converted URL.
     */
    private static function convertInternalPageUrl($url, $kirby)
    {
        $pageId = substr($url, 8);
        $page = $kirby->site()->page('@' . $pageId);
        if ($page) {
            $url = $page->url();
            return $url;
        } else {
            return $url;
        }
    }

    /**
     * Converts a relative URL to an absolute URL.
     * 
     * @param string $url The URL to convert.
     * @return string The converted URL.
     */
    private static function convertRelativeUrl($url)
    {
        $baseUrl = self::getBaseUrl();
        $url = $baseUrl . '/' . ltrim($url, '/');
        return $url;
    }

    /**
     * Retrieves the base URL.
     * 
     * @return string The base URL.
     */
    private static function getBaseUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . $host;
    }

    /**
     * Checks if a URL is absolute.
     * 
     * @param string $url The URL to check.
     * @return bool True if the URL is absolute, false otherwise.
     */
    private static function isAbsoluteUrl($url)
    {
        return preg_match("~^(?:f|ht)tps?://~i", $url);
    }

    /**
     * Checks if a URL is a special URL.
     * 
     * @param string $url The URL to check.
     * @return bool True if the URL is special, false otherwise.
     */
    private static function isSpecialUrl($url)
    {
        return preg_match("~^mailto:~i", $url) || preg_match("~^tel:~i", $url) || $url[0] === '#';
    }
}