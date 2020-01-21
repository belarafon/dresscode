jQuery(document).ready(function ($) {

                $(document.body).on('updated_checkout updated_shipping_method', function (event, xhr, data) {
                    //TODO this method should be more abstract
                    var value = $('input[name^=shipping_method][type=radio]:checked').val();
              
                    if (!value) {
                      value = $('input#shipping_method_0').val();
                    }

                    // console.log( value );
                    
                    if ( value == 'ukrposhta' ) {
                      $('#billing_address_1_field').hide();
                      $('#billing_address_1').hide();
                      $("#billing_address_1").attr("required", false);
                      $('#billing_address_1').val('-');
                      $('#billing_address_2').hide();
                      $("#billing_address_2").prop("required", false);
                      $('#billing_city_field').hide();
                      $('#billing_city').hide();
                      $('#billing_city').val('-');
                      $("#billing_city").prop("required", false);
                      $('#billing_state_field').hide();
                      $('#billing_state').hide();
                      $("#billing_state").prop("required", false);
                      $('#billing_state').val('-');
                    } else if ( value == 'nova_poshta_shipping_method' ) {
						
					}else {
                      $('#billing_address_1').show();
                      $('#billing_address_1').val('');
                      $('#billing_address_1_field').show();
                      $('#billing_address_2').show();
                      $('#billing_city_field').show();
                      $('#billing_city').show();
                      $('#billing_city').val('');
                      $('#billing_state_field').show();
                      $('#billing_state').show();
                      $('#billing_state').val('');
                    }

                });


            });