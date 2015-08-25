<?php

namespace ApiHelper\Transformers;

use Craft\BaseElementModel;

class EntryTransformer extends BaseTransformer implements TransformerInterface
{
    public function transformModel(BaseElementModel $entry)
    {
        return [
            'id'            => (int) $entry->id,
            'enabled'       => (int) $entry->enabled,
            'archived'      => (int) $entry->archived,
            'locale'        => $entry->locale,
            'localeEnabled' => (int) $entry->localeEnabled,
            'slug'          => $entry->slug,
            'uri'           => $entry->uri,
            'dateCreated'   => $entry->dateCreated,
            'dateUpdated'   => $entry->dateUpdated,
            'root'          => $entry->root,
            'lft'           => (int) $entry->lft,
            'rgt'           => (int) $entry->rgt,
            'level'         => (int) $entry->level,
            'sectionId'     => (int) $entry->sectionId,
            'typeId'        => (int) $entry->typeId,
            'authorId'      => (int) $entry->authorId,
            'postDate'      => $entry->postDate,
            'expiryDate'    => $entry->expiryDate,
            'parentId'      => (int) $entry->parentId,
            'revisionNotes' => $entry->revisionNotes,
        ];
    }
}
