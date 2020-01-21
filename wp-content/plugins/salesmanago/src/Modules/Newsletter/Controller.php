<?php

namespace bhr\Modules\Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Controller
{
    const MAP_NAME    = 'mappedName';
    const NEWS_CONT   = 'newsletterContent';
    const COOKIE_NAME = 'smoptst';

    private $NewsletterContext;

    public function __construct($newsletterType = null) {
		$Context = new Context(new Newsletter());
		$this->NewsletterContext = $Context->getContext($newsletterType);
    }

    public function getConfig()
    {
        return $this->NewsletterContext->getConfig();
    }

    public function setConfig($params)
    {
        return $this->NewsletterContext->setConfig($params);
    }
}
