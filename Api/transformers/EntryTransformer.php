<?php

namespace Craft;

use ApiHelper\Transformers\BaseTransformer;

class EntryTransformer extends BaseTransformer
{
    public function transformModel(BaseElementModel $entry)
    {
        return [
            'id' => $entry->id
        ];
    }
}
