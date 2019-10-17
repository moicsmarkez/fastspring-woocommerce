(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    $(document).ready(function($){
            $('#_producto_fastspring').select2({
                    theme: "classic"
            });
             $('#_producto_fastspring').on('change', function(){
                 if($(this).val() != ''){
                     $.ajax(
                     {
                        url: 'admin-ajax.php?action=fastspring_product_update',
                        method: 'post',
                        data: {path_product: $(this).val()},
                        beforeSend: function(){
                            $(this).prop("disabled", true);
                            $("#overlay_loading").show();
                            console.log('ENviando PRobando');
                        },
                        success: function( response ){
                            $('.datos-producto-fs').html(response.data);
                            $("#overlay_loading").hide();
                            $(this).prop("disabled", false);
                        }
                     });
                 }else {
                     $('.datos-producto-fs').html('');
                 }
             });
    });

})( jQuery );