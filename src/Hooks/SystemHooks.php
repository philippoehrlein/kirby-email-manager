<?php

return [
    'system.loadPlugins:after' => function() {
        kirby()->extend([
            'options' => [
                'philippoehrlein.kirby-email-manager.templates' => option('philippoehrlein.kirby-email-manager.templates')()
            ]
        ]);
    },
];