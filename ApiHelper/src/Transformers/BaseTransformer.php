<?php

namespace ApiHelper\Transformers;

use League\Fractal\TransformerAbstract;

class BaseTransformer extends TransformerAbstract
{
    public function transform(array $array)
    {
        return $this->transformModel($array[0]);
    }
}
