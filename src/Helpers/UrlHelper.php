<?php

namespace KirbyEmailManager\Helpers;

class UrlHelper
{
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

    private static function convertXWebdocUrl($url, $kirby)
    {
        error_log("Processing x-webdoc URL: " . $url);
        $pagePos = strpos($url, '/@/page/');
        if ($pagePos !== false) {
            $pageId = substr($url, $pagePos + 8);
            $page = $kirby->site()->page('@' . $pageId);
            if ($page) {
                $url = $page->url();
                error_log("Found page for x-webdoc URL: " . $url);
                return $url;
            } else {
                error_log("Page not found for x-webdoc URL: " . $url . " (PageID: " . $pageId . ")");
                return $url; // Behalte den ursprünglichen Link bei
            }
        } else {
            $url = preg_replace('/^x-webdoc:\/\/[^\/]+/', '', $url);
            $url = self::getBaseUrl() . '/' . ltrim($url, '/');
            error_log("Converted x-webdoc URL to: " . $url);
            return $url;
        }
    }

    private static function convertInternalPageUrl($url, $kirby)
    {
        $baseUrl = self::getBaseUrl();

        $pageId = substr($url, 8);
        $page = $kirby->site()->page('@' . $pageId);
        if ($page) {
            $url = $baseUrl . '/' . $page->url();
            error_log("Internal page link converted: " . $url);
            return $url;
        } else {
            error_log("Page not found for link: " . $url . " (PageID: " . $pageId . ")");
            return $url; // Behalte den ursprünglichen Link bei
        }
    }

    private static function convertRelativeUrl($url)
    {
        $baseUrl = self::getBaseUrl();
        $url = $baseUrl . '/' . ltrim($url, '/');
        error_log("Relative link converted: " . $url);
        return $url;
    }

    private static function getBaseUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        error_log("Base URL: " . $protocol . $host);
        return $protocol . $host;
    }

    private static function isAbsoluteUrl($url)
    {
        return preg_match("~^(?:f|ht)tps?://~i", $url);
    }

    private static function isSpecialUrl($url)
    {
        return preg_match("~^mailto:~i", $url) || preg_match("~^tel:~i", $url) || $url[0] === '#';
    }
}