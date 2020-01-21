<?php

namespace bhr\Modules\Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Modules\Newsletter\States\StateInput;
use bhr\Modules\Newsletter\States\StateNoActive;
use bhr\Modules\Newsletter\States\StateMapper;

class Context {

	private $Newsletter;
	private $context;

	public function __construct(Newsletter $Newsletter)
	{
		$this->Newsletter = $Newsletter;
	}

	public function getContext($type = null)
	{
		$newsletterType = ($type != null)
			? $type
			: $this->Newsletter->getType();

		switch ($newsletterType) {
			case 'newsletter':
				$this->context = new StateInput();
				break;
			case 'mapper':
				$this->context = new StateMapper();
				break;
			default:
				$this->context = new StateNoActive();
				break;
		}

		return $this->context;
	}
}