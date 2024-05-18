/* 
 * Hooks into payment methods radio buttons
 * 
 * depends on woocommerce.js
 */
jQuery(function() {

	jQuery('.payment_methods input.input-radio').live('change', function()
		{
			jQuery('body').trigger('update_checkout');
		});
		
});