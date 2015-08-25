<?php

namespace Api\Http\Response\Statuses;

class SuccessfulStatus extends AbstractStatus;
{
    public function ok($message = 'OK')
    {
        return $this->setStatusCode($message, 200);
    }

    public function created($message = 'Created')
    {
        return $this->setStatusCode($message, 201);
    }

    public function accepted($message = 'Accepted')
    {
        return $this->setStatusCode($message, 202);
    }

    public function nonAuthoritiveInformation($message = 'Non-Authorative Information')
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
