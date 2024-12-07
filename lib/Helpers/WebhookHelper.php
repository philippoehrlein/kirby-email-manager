<?php

namespace KirbyEmailManager\Helpers;

class WebhookHelper 
{
    /**
     * Prüft ob Webhooks für dieses Template aktiviert sind
     */
    public static function hasHandlers(array $templateConfig): bool
    {
        return !empty($templateConfig['webhooks']) && 
               !empty(kirby()->option('philippoehrlein.kirby-email-manager.webhooks.handlers', []));
    }

    /**
     * Führt registrierte Webhooks aus wenn sie für das Event konfiguriert sind
     */
    public static function trigger(string $event, array $data, array $templateConfig): void 
    {
        error_log('Template Config: ' . print_r($templateConfig, true));
        error_log('Registered Handlers: ' . print_r(kirby()->option('philippoehrlein.kirby-email-manager.webhooks.handlers', []), true));

        if (!self::hasHandlers($templateConfig)) {
            error_log('No handlers found');
            return;
        }

        foreach ($templateConfig['webhooks'] as $webhook) {
            error_log('Processing Webhook: ' . print_r($webhook, true));

            if (!isset($webhook['handler']) || 
                !isset($webhook['events']) || 
                !in_array($event, $webhook['events'])) {
                continue;
            }

            $registeredHandlers = kirby()->option('philippoehrlein.kirby-email-manager.webhooks.handlers', []);
            if (!isset($registeredHandlers[$webhook['handler']])) {
                error_log('Handler not found: ' . $webhook['handler']);
                continue;
            }

            try {
                $handler = $registeredHandlers[$webhook['handler']];
                $handler($event, $data);
            } catch (\Exception $e) {
                LogHelper::logError('Webhook error: ' . $e->getMessage());
            }
        }
    }
}
