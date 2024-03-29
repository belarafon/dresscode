<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Field: Sub Heading
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
class CSFramework_Option_subheading extends CSFramework_Options {

  public function __construct( $field, $value = '', $unique = '' ) {
    parent::__construct( $field, $value, $unique );
  }

  public function output() {
    echo $this->element_before();
    echo $this->field['content'];
    if(isset($this->field['desc']) && $this->field['desc'] != ''){
        echo '<div class="desc_subheading">'.$this->field['desc'].'</div>';
    }
    echo $this->element_after();

  }

}
