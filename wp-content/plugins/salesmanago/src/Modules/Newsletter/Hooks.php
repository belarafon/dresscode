<?php

namespace bhr\Modules\Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bhr\Helper\ModulesConfigManager as MCM;

class Hooks
{
    public $Newsletter;
    private $NewsletterContext;

    public function __construct()
    {
	    $this->Newsletter = new Newsletter();
	    $this->initHooks();
    }

    public function initHooks()
    {
	    $this->Newsletter
		    ->setDefaultLang('EN');

    	$Context = new Context($this->Newsletter);
    	$this->NewsletterContext = $Context->getContext();

        if ($this->NewsletterContext->setFront() !== false) {
	        if (MCM::getInstance()
	               ->isEnabled(MCM::SM_WC_NAME_SP)
	        ) {
		        add_action(
			        'woocommerce_register_form',
			        [ $this, 'showView' ],
			        14
		        );
		        add_action(
			        'woocommerce_after_checkout_registration_form',
			        [ $this, 'showView' ],
			        15
		        );
	        }
            /*
            add_action(
                'woocommerce_edit_account_form',
                [$this, 'showView'],
                16
            );
            */
	        if (MCM::getInstance()
	               ->isEnabled(MCM::SM_WP_NAME_SP)
	        ) {
		        add_action(
			        'register_form',
			        [$this, 'showView'],
			        14
		        );
	        }
        }

    }

    public function showView()
    {
	    $userLocal = get_user_locale();
	    $userLocal = ( strlen( $userLocal ) > 3 )
		    ? substr( $userLocal, 3, 2 )
		    : strtoupper( $userLocal );

	    $Context = new Context($this->Newsletter->setDefaultLang( 'EN' ));

	    $this->NewsletterContext = $Context->getContext()->setLang( $userLocal )->setDefaultLang( 'EN' );

	    echo $this->NewsletterContext->setFront();
    }
}
