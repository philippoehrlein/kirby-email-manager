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
        $pagePos = strpos($url, '/@/page/');
        if ($pagePos !== false) {
            $pageId = substr($url, $pagePos + 8);
            $page = $kirby->site()->page('@' . $pageId);
            if ($page) {
                $url = $page->url();
                return $url;
            } else {
                return $url; // Behalte den ursprünglichen Link bei
            }
        } else {
            $url = preg_replace('/^x-webdoc:\/\/[^\/]+/', '', $url);
            $url = self::getBaseUrl() . '/' . ltrim($url, '/');
            return $url;
        }
    }

    private static function convertInternalPageUrl($url, $kirby)
    {
        $pageId = substr($url, 8);
        $page = $kirby->site()->page('@' . $pageId);
        if ($page) {
            $url = $page->url();
            return $url;
        } else {
            return $url; // Behalte den ursprünglichen Link bei
        }
    }

    private static function convertRelativeUrl($url)
    {
        $baseUrl = self::getBaseUrl();
        $url = $baseUrl . '/' . ltrim($url, '/');
        return $url;
    }

    private static function getBaseUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
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