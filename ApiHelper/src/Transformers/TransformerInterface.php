<?php

namespace ApiHelper\Transformers;

use Craft\BaseElementModel;

interface TransformerInterface
{
    public function transformModel(BaseElementModel $model);
}
