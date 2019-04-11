/**
 * @file
 * Javascript for Field Example.
 */

jQuery(document).ready(function(){

    jQuery(".mathematical_formatter").mouseenter(function(){
        jQuery(this).html(jQuery(this).attr('formula')+' = '+jQuery(this).attr('compute'));
    });

    jQuery(".mathematical_formatter").mouseout(function(){
        jQuery(this).html(jQuery(this).attr('formula'));
    });
});