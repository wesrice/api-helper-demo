<?php

namespace ApiHelper\Transformers;

use Craft\BaseElementModel;

class ErrorTransformer extends BaseTransformer
{
    public function transformModel(array $error)
    {
        return $error;
    }
}
