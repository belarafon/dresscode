(function ($) {
    "use strict"; // Start of use strict
    
    /* ---------------------------------------------
     Scripts ready
     --------------------------------------------- */
    $(document).ready(function() {
        if( $('.nexio_vc_taxonomy').length > 0){
            $('.nexio_vc_taxonomy').chosen();
        }
        $(document).on('change','.nexio_select_preview',function(){
            var url = $(this).find(':selected').data('img');
            $(this).parent('.container-select_preview').find('.image-preview img').attr('src',url);
        });
    });
    $(document).ajaxComplete(function (event, xhr, settings) {
        if( $('.nexio_vc_taxonomy').length > 0){
            $('.nexio_vc_taxonomy').chosen();
        }
    });

})(jQuery); // End of use strict