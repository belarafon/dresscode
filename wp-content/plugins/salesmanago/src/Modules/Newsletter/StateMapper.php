<?php

namespace bhr\Modules\Newsletter;

class StateMapper implements State
{
    protected $newsletter;
    protected $type;

    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
        $this->type = 'mapper';
    }

    public function setConfig($params)
    {
        $data = [
            'type'       => $this->type,
            Newsletter::MAP_NAME => htmlspecialchars($params[Newsletter::MAP_NAME]),
        ];

        return $this->newsletter
            ->getModel()
            ->setNewsletterConfig($data);
    }

    public function setFront()
    {
        return false;
    }

    public function getOptStateArr()
    {
        $state = $this->getSubscriberStateFromRequest();

        if ($state) {
            return [
                'forceOptIn'       => $state,
                'forceOptOut'      => !$state,
                'forcePhoneOptIn'  => $state,
                'forcePhoneOptOut' => !$state
            ];
        }

        return $this->newsletter
            ->getModel()
            ->getDefaultContactState();
    }

    public function getSubscriberStateFromRequest()
    {
        $config = $this->newsletter->getConfig();

        if (isset($_REQUEST[$config[Newsletter::MAP_NAME]])) {
            return true;
        }
        return false;
    }
}
