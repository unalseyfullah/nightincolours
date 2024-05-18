(function ( $ ) {
	"use strict";

	$(function () {


  jQuery('input.icheck').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass: 'iradio_flat-green'
  });
  jQuery('input.icheck_red').iCheck({
    checkboxClass: 'icheckbox_flat-red',
    
  });
  //Help tooltip
  jQuery('.help-ico').on({
    mouseenter: function () {
        jQuery(this).children('.help-tooltip').fadeIn('slow');
    },
    mouseleave: function () {
        jQuery(this).children('.help-tooltip').fadeOut('slow');
    }
  });
  
  jQuery('#tabulka-doprava').on('click','.remove-tr',function(){
      jQuery(this).closest('tr').remove();
  });
  jQuery('#tabulka-doprava-123').on('click','.remove-tr',function(){
      jQuery(this).closest('tr').remove();
  });
  
  
  jQuery('#pridatdopravu').on('click',function(){
  
      var dopravaid = jQuery('#doprava').val();
      //alert(dopravaid);
      jQuery('#tabulka-doprava tbody').append('<tr><td>'+dopravaid+'<input type="hidden" name="delivery_id['+dopravaid+']" value="'+dopravaid+'"></td><td><input type="text" name="delivery_price['+dopravaid+']" value=""></td><td><input type="text" name="delivery_price_cod['+dopravaid+']" value=""></td><td class="td_center"><input class="icheck" type="checkbox" name="delivery_active['+dopravaid+']"></td><td class="td_center"><span class="btn btn-danger btn-sm remove-tr" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove"><i class="fa fa-times"></i></span></td></tr>');
  });  
  jQuery('#pridatdopravu_123').on('click',function(){
      jQuery('#tabulka-doprava-123 tbody').append('<tr><td><input type="text" name="delivery_name[]" value="" /></td><td><input type="text" name="delivery_price[]" value="" /></td><td class="td_center"><span class="btn btn-danger btn-sm remove-tr" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove"><i class="fa fa-times"></i></span></td></tr>');
  });  
  
  
  jQuery('#pridataltimg').on('click',function(){
  
      var textfieldid = jQuery('#altimg-wrap .imgurl_alternative').last().data('id');
      
      textfieldid++;
      jQuery('#altimg-wrap').append('<p class="form-field imgurl_alternative_field"><label for="imgurl_alternative">Alternativní obrázek</label><input type="text" class="imgurl_alternative" id="alt'+textfieldid+'" data-id="'+textfieldid+'" name="imgurl_alternative[]" value="" /><input type="button" class="btn btn-info btn-mini alt-image-button" value="Nahrej obrázek" style="width:auto;margin-left:10px;" /><span class="btn btn-danger btn-mini remove-altimg" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove"><i class="fa fa-times"></i></span></p>');
  });  
  
  
  jQuery('#altimg-wrap').on('click','.remove-altimg',function(){
      jQuery(this).closest('.imgurl_alternative_field').remove();
  });  
   //Upload image button
  jQuery('#altimg-wrap').on('click','.alt-image-button',function(){
    var textfieldid = jQuery(this).prev().attr('id');
      
    wp.media.editor.send.attachment = function(props, attachment){
      jQuery('#' + textfieldid).val(attachment.url);
      
    }
    wp.media.editor.open(this);
		return false;
        
  });
  
  
  
  
  
  
  jQuery('.pridataltimg').on('click',function(){
  
      var wrapid = jQuery(this).data('loop');
  
      var textfieldid = jQuery('.altimg-wrap'+wrapid+' .imgurl_alternative').last().data('id');
      
      textfieldid++;
      jQuery('.altimg-wrap'+wrapid).append('<p class="form-field imgurl_alternative_field"><label for="imgurl_alternative">Alternativní obrázek</label><input type="text" class="imgurl_alternative" id="alt'+wrapid+textfieldid+'" name="_variation_imgurl_alternative['+wrapid+'][]" value="" /><input type="button" class="btn btn-info btn-mini alt-image-button" value="Nahrej obrázek" style="width:auto;" data-loop="'+wrapid+'" /><span class="btn btn-danger btn-mini remove-altimg" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove"><i class="fa fa-times"></i></span></p>');
  });  
  
  
  jQuery('.altimg-wrap').on('click','.remove-altimg',function(){
      jQuery(this).closest('.imgurl_alternative_field').remove();
  });  
  
   //Upload image button
  jQuery('.altimg-wrap').on('click','.alt-image-button',function(){
    var wrapid = jQuery(this).data('loop');
    var textfieldid = jQuery(this).prev().attr('id');
    
    wp.media.editor.send.attachment = function(props, attachment){
      jQuery('#'+textfieldid).val(attachment.url);
    }
    wp.media.editor.open(this);
		return false;
        
  });  
    
    
    
    
  jQuery('body').on('click','.add-param',function(){
     var id = jQuery(this).data('par');
  
     jQuery(this).before('<fieldset><input type="text" name="nazev_parametru_'+id+'[]" placeholder="Název parametru" /><input type="text" name="hodnota_parametru_'+id+'[]" placeholder="Hodnota parametru" /><span class="btn btn-danger btn-sm remove-param"><i class="fa fa-times"></i></span></fieldset>');
  });  
  
  
  jQuery('body').on('click','.remove-param',function(){
     jQuery(this).closest('fieldset').remove();
  });
  
//  }  
    
    
    
    
    
    
      /**
		 * Save single product line data
		 *
		 */              
    jQuery('.save-product-data').on('click', function(){
       jQuery('.lineloader').css('display','block');
       var product = jQuery(this).data('product');
       
       
       var custom_product_title  = jQuery('.custom_product_title' + product).val();
       var ean                   = jQuery('.ean' + product).val();
       var isbn                  = jQuery('.isbn' + product).val();
       var heureka_cpc           = jQuery('.heureka_cpc' + product).val();
       var heureka_cpc_sk        = jQuery('.heureka_cpc_sk' + product).val();
       var accessory             = jQuery('.accessory' + product).val();
       var heureka_category      = jQuery('.heureka_category' + product).val();
       var heureka_category_sk   = jQuery('.heureka_category_sk' + product).val();
       var zbozi_category        = jQuery('.zbozi_category' + product).val();
       var delivery_date         = jQuery('.delivery_date' + product).val();
       var dues                  = jQuery('.dues' + product).val();
       var heureka_item_type     = jQuery('.heureka_item_type' + product).val();
       var zbozi_unfeatured      = jQuery('.zbozi_unfeatured' + product).val();
       var zbozi_extra_message   = jQuery('.zbozi_extra_message' + product).val();
       var product_deadline_time = jQuery('.product_deadline_time' + product).val();
       var product_delivery_time = jQuery('.product_delivery_time' + product).val();
       var video_url             = jQuery('.video_url' + product).val();
       var manufacturer          = jQuery('.manufacturer' + product).val();
       var zbozi_cpc             = jQuery('.zbozi_cpc' + product).val();
       var srovname_toll         = jQuery('.srovname_toll' + product).val();
       var pricemania_shipping   = jQuery('.pricemania_shipping' + product).val();
       
       var data = {
            action                : 'woo_xml_save_one_product',
            product               : product,
            custom_product_title  : custom_product_title,
            ean                   : ean,
            isbn                  : isbn,
            heureka_cpc           : heureka_cpc,
            heureka_cpc_sk        : heureka_cpc_sk,
            accessory             : accessory,
            heureka_category      : heureka_category,
            heureka_category_sk   : heureka_category_sk,
            zbozi_category        : zbozi_category,
            delivery_date         : delivery_date,
            dues                  : dues,
            heureka_item_type     : heureka_item_type,
            zbozi_unfeatured      : zbozi_unfeatured,
            zbozi_extra_message   : zbozi_extra_message,
            product_deadline_time : product_deadline_time,
            product_delivery_time : product_delivery_time,
            video_url             : video_url,
            manufacturer          : manufacturer,
            zbozi_cpc             : zbozi_cpc,
            srovname_toll         : srovname_toll,
            pricemania_shipping   : pricemania_shipping
       };
        jQuery.post(ajaxurl, data, function(response){
           
          jQuery('.lineloader').css('display','none'); 
        
        });
       
    });
    
    
    
    
    
    /**
		 * Save product variation line data
		 *
		 */              
    jQuery('.save-product-variation').on('click', function(){
       jQuery('.lineloader').css('display','block');
       var product = jQuery(this).data('product');
       
       
       var variation_heureka_title    = jQuery('.variation_heureka_title' + product).val();
       var variation_heureka_category = jQuery('.variation_heureka_category' + product).val();
       var variation_video_url        = jQuery('.variation_video_url' + product).val();
       var variation_delivery_date    = jQuery('.variation_delivery_date' + product).val();
       var variation_accessory        = jQuery('.variation_accessory' + product).val();
       var variation_dues             = jQuery('.variation_dues' + product).val();
       
       
       var data = {
            action                     : 'woo_xml_save_variation_product',
            product                    : product,
            variation_heureka_title    : variation_heureka_title,
            variation_heureka_category : variation_heureka_category,
            variation_video_url        : variation_video_url,
            variation_delivery_date    : variation_delivery_date,
            variation_accessory        : variation_accessory,
            variation_dues             : variation_dues
       };
       
        jQuery.post(ajaxurl, data, function(response){
           
          jQuery('.lineloader').css('display','none'); 
        
        });
       
    });
    
     
    
    
    
    
    
    
    
    
    
	
	});

}(jQuery));