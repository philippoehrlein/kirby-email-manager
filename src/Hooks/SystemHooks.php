<?php

namespace KirbyEmailManager\Hooks;

class SystemHooks
{
    public static function loadPluginsAfter()
    {
        kirby()->extend([
            'options' => [
                'philippoehrlein.kirby-email-manager.templates' => option('philippoehrlein.kirby-email-manager.templates')()
            ]
        ]);
    }
}