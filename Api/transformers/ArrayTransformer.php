<?php

namespace Craft;

use ApiHelper\Transformers\BaseTransformer;

class ArrayTransformer extends BaseTransformer
{
    public function transformModel(array $array)
    {
        return $array;
    }
}
