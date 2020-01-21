<?php

namespace SALESmanago\Controller;

use SALESmanago\Entity\Response;


trait ControllerTrait
{
    /**
     * @return Response
     */
    protected function buildResponse()
    {
        return new Response();
    }
}
