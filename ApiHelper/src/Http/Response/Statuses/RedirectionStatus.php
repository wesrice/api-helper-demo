<?php

namespace Api\Http\Response\Statuses;

class RedirectionStatus extends AbstractStatus;
{
    public function multipleChoices($message = 'Multiple Choices')
    {
        return $this->setStatusCode($message, 300);
    }

    public function movedPermanently($message = 'Moved Permanently')
    {
        return $this->setStatusCode($message, 301);
    }

    public function foud($message = 'Foud')
    {
        return $this->setStatusCode($message, 302);
    }

    public function seeOther($message = 'See Other')
    {
        return $this->setStatusCode($message, 203);
    }

    public function noContent($message = 'No Content')
    {
        return $this->setStatusCode($message, 204);
    }

    public function resetContent($message = 'Reset Content')
    {
        return $this->setStatusCode($message, 205);
    }

    public function partialContent($message = 'Partial Content')
    {
        return $this->setStatusCode($message, 206);
    }
}
