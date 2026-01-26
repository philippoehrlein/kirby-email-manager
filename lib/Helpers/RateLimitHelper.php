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
        if (!($templateConfig['ratelimit']['trackip'] ?? false)) {
            return true;
        }
        
        $cache = kirby()->cache('philippoehrlein.kirby-email-manager.ip');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $now = time();
        
        // Validate IP address
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = 'unknown';
        }
        
        // Get salt with fallback
        // For maximum security, set a custom salt in your config:
        $salt = kirby()->option(
            'philippoehrlein/kirby-email-manager.ip.salt', 
            'kem-default-salt-v1-change-in-production'
        );
        
        $ipHash = hash('sha256', $ip . $salt);
        
        $attemptsKey = "ratelimit.{$ipHash}.attempts";
        $blockKey = "ratelimit.{$ipHash}.blocked";
        $blockCountKey = "ratelimit.{$ipHash}.blockcount";
        
        $blockData = $cache->get($blockKey);
        
        if ($blockData) {
            $blockTime = is_array($blockData) && isset($blockData['created']) 
                ? (int)$blockData['created'] 
                : time() - self::BLOCK_DURATION;
            $blockDuration = is_array($blockData) && isset($blockData['duration'])
                ? (int)$blockData['duration']
                : self::BLOCK_DURATION;
            $expiresAt = $blockTime + $blockDuration;
            
            if ($now >= $expiresAt) {
                $cache->remove($blockKey);
                return true;
            }
            
            return false;
        }
        
        $attempts = $cache->get($attemptsKey, []);        
        $attempts = array_filter($attempts, fn($time) => $time > ($now - self::TIME_WINDOW));
        
        if (count($attempts) >= ($templateConfig['ratelimit']['maxattempts'] ?? self::MAX_ATTEMPTS)) {
            $blockCount = $cache->get($blockCountKey, 0) + 1;
            $cache->set($blockCountKey, $blockCount, 86400);
            
            if ($blockCount >= self::MAX_BLOCKS) {
                $cache->set($blockKey, [
                    'created' => time(),
                    'duration' => self::PERMANENT_BLOCK
                ], self::PERMANENT_BLOCK);
            } else {
                $duration = self::BLOCK_DURATION * pow(2, $blockCount - 1);
                $cache->set($blockKey, [
                    'created' => time(),
                    'duration' => $duration
                ], $duration);
            }
            return false;
        }
        
        $attempts[] = $now;
        $success = $cache->set($attemptsKey, $attempts, self::TIME_WINDOW);
        
        return true;
    }
}