<?php

namespace KirbyEmailManager\Helpers;

/**
 * RateLimitHelper class provides methods to check and handle rate limits.
 * Author: Philipp Oehrlein
 * Version: 1.0.0
 */
class RateLimitHelper
{
    private const MAX_ATTEMPTS = 5;        // max attempts
    private const TIME_WINDOW = 300;       // 5 minutes
    private const BLOCK_DURATION = 1800;   // 30 minutes
    private const MAX_BLOCKS = 5;          // after 5 blocks very long blocked
    private const PERMANENT_BLOCK = 604800; // 1 week
    
    public static function checkRateLimit(array $templateConfig): bool
    {
        if (!($templateConfig['rate_limit']['track_ip'] ?? false)) {
            return true;
        }
        
        $cache = kirby()->cache('philippoehrlein.kirby-email-manager.ip');
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $now = time();
        
        $ipHash = hash('sha256', $ip . kirby()->option('philippoehrlein/kirby-email-manager.ip.salt', ''));
        
        $attemptsKey = "rate_limit.{$ipHash}.attempts";
        $blockKey = "rate_limit.{$ipHash}.blocked";
        $blockCountKey = "rate_limit.{$ipHash}.block_count";
        
        if ($cache->get($blockKey)) {
            return false;
        }
        
        $attempts = $cache->get($attemptsKey, []);        
        $attempts = array_filter($attempts, fn($time) => $time > ($now - self::TIME_WINDOW));
        
        if (count($attempts) >= ($templateConfig['rate_limit']['max_attempts'] ?? self::MAX_ATTEMPTS)) {
            $blockCount = $cache->get($blockCountKey, 0) + 1;
            $success = $cache->set($blockCountKey, $blockCount, 86400);
            
            if ($blockCount >= self::MAX_BLOCKS) {
                $success = $cache->set($blockKey, true, self::PERMANENT_BLOCK);
            } else {
                $duration = self::BLOCK_DURATION * pow(2, $blockCount - 1);
                $success = $cache->set($blockKey, true, $duration);
            }
            return false;
        }
        
        $attempts[] = $now;
        $success = $cache->set($attemptsKey, $attempts, self::TIME_WINDOW);
        
        return true;
    }
}