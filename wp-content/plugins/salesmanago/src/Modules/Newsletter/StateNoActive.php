<?php

namespace bhr\Modules\Newsletter;

class StateNoActive implements State
{
    protected $newsletter;
    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function setConfig($params)
    {
        return false;
    }

    public function getConfig()
    {
        return false;
    }

    public function setFront()
    {
        return false;
    }

    public function getOptStateArr()
    {
        $state = $this->newsletter
            ->getModel()
            ->getDefaultContactState();

        return $state;
    }

    public function getSubscriberStateFromRequest()
    {
        return false;
    }
}