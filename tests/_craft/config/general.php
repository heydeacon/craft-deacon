<?php

use craft\helpers\App;

return [
    'devMode' => true,
    'securityKey' => App::env('CRAFT_SECURITY_KEY'),
    // Rendering the settings page publishes control panel assets. Keep them out of the repo.
    'resourceBasePath' => '@storage/cpresources',
];
