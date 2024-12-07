<?php

use KirbyEmailManager\Hooks\SystemHooks;

return [
  'system.loadPlugins:after' => function () {
      SystemHooks::loadPluginsAfter();
      SystemHooks::extendTranslations();
  },
];