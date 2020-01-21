<?php


namespace bhr\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


interface ContactModelInterface
{
    public function get();
    public function getSmContactOptInStatus();
    public function setParameters($array);
}
