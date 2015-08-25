<?php

namespace Api\Http\Response\Statuses;

class InformationalStatus extends AbstractStatus;
{
    public function continue($message = 'Continue')
    {
        return $this->setStatusCode($message, 100);
    }

    public function switchingProtocols($message = 'Switching Protocols')
    {
        return $this->setStatusCode($message, 101);
    }
}
