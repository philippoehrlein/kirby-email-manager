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
                
                if (strpos($url, '/@/page/') === 0) {
                    // Interner Seitenlink
                    $pageId = substr($url, 8);
                    $page = $kirby->site()->page('@' . $pageId);
                    if ($page) {
                        $url = $page->url();
                        error_log("Internal page link converted: " . $matches[1] . " -> " . $url);
                    } else {
                        error_log("Page not found for ID: @" . $pageId);
                    }
                } elseif (strpos($url, '/@/file/') === 0) {
                    // File-Link
                    $fileId = substr($url, 8);
                    $file = $kirby->file('@' . $fileId);
                    if ($file) {
                        $url = $file->url();
                        error_log("File link converted: " . $matches[1] . " -> " . $url);
                    } else {
                        error_log("File not found for ID: @" . $fileId);
                    }
                } elseif (!preg_match("~^(?:f|ht)tps?://~i", $url) && 
                          !preg_match("~^mailto:~i", $url) && 
                          !preg_match("~^tel:~i", $url) &&
                          $url[0] !== '#') {
                    // Relative URL (nicht mailto:, tel: oder Anker)
                    $url = $kirby->url() . '/' . ltrim($url, '/');
                    error_log("Relative link converted: " . $matches[1] . " -> " . $url);
                }
                
                return str_replace($matches[1], $url, $matches[0]);
            },
            $content
        );
    }
}