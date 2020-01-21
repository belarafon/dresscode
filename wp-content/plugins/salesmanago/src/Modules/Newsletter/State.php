<?php

namespace bhr\Modules\Newsletter;

interface State
{
    public function setConfig($params);
    public function setFront();
    public function getOptStateArr();
    public function getSubscriberStateFromRequest();
}
