<?php

namespace heydeacon\deacon\models;

use craft\base\Model;

class Settings extends Model
{
    public string $key = '';

    public mixed $context = null;

    public mixed $pageViews = true;
}
