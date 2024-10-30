jQuery( document ).ready(function($) {
	'use strict';

    //setting tab open
    $('#cd_api_true').on('click',function(event){
        $('.cd_settings_div').show();
    });
    
    // Api Add
    $('#cd_api_key_add').on('click',function(event){
        var key = $('#cd_api_key_id').val();
        if(key.length == 32){
            jQuery.ajax({
                url: frontendajax.ajaxurl,
                type: 'post',
                data: {'key':key, 'action':'clickdesigns_add_api'},
                success: function(response) {
                    var result = JSON.parse(response);
                    if(result.status == 'false'){
                        toastr.error(result.msg);
                    }else{
                        toastr.success(result.msg);
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    }
                }
            });
        } else {
            toastr["error"]("Invaild Api Key");
        }
    });

    // Api Add
    $('#cd_api_key_remove').on('click',function(event){
        jQuery.ajax({
            url: frontendajax.ajaxurl,
            type: 'POST',
            data: {'action':'clickdesigns_remove_api'},
            success: function(response) {
				console.log(response);
				
				
                var result = JSON.parse(response);
                if(result.status == 'false'){
                    toastr.error(result.msg);
                }else{
                    toastr.success(result.msg);
                    setTimeout(function () {
                        location.reload();
                    }, 2000);                    
                }
            }
        });
    });

    // ClickDesign tab add in media 
    jQuery(document).ready(function ($) {
            jQuery.ajax({
                url: frontendajax.ajaxurl,
                type: 'POST',
                data: {'action':'clickdesigns_tab_media'},
                success: function(response) {
					
                    var result = JSON.parse(response);
                    if(result.status == 'true'){
                        $(wp.media).on("click", ".media-router .media-menu-item", function (e) {
                            if (e.target.innerText == "ClickDesigns") {doMyTabContent()};
                            $('.cd-loader').show();
                            $.ajax({
                                type: 'POST',
                                url: frontendajax.ajaxurl,
                                data: {'action':'clickdesigns_api_images'},
                                success: function(response){
                                    $(".cd-loader").delay(1).fadeOut("slow");
                                    $('.cd_media_wrapper').html(response);
                                    $(".cd_media_tab a:first-child").addClass('active');
                                }
                            });	
                        });
                        if ( wp.media ) {
                            wp.media.view.Modal.prototype.on("open", function () {
                                if ($("body").find(".media-modal-content").last().find(".media-router .media-menu-item.active").text() == "ClickDesigns"){
                                    doMyTabContent();
                                }
								
                                $('.cd-loader').show();                            
                                $.ajax({
                                    type: 'POST',
                                    url: frontendajax.ajaxurl,
                                    data: {'action':'clickdesigns_api_images'},
                                    success: function(response){
                                        $(".cd-loader").delay(1).fadeOut("slow");
                                        $('.cd_media_wrapper').html(response);
                                        $(".cd_media_tab a:first-child").addClass('active');
                                    }
                                });
                            });
                        }
                        
                    }
                }
            });
    });

    // ClickDesign tab Content 
    function doMyTabContent() {
		var html = '<div class="cd_media_wrapper"></div><div class="cd-loader" style="display: none;"><img src="https://cdn1.clickdesigns.com/images/loader.gif"></div>';  
		jQuery("body .media-modal-content .media-frame-content").html(html); 			  
	} 

    // Get Designs tab images get 
    $(document).on('click','#cd_get_designs',function(event){
        $('.cd-loader').show();    
        $('.cd-media-tab a').removeClass('active');
        $(this).addClass('active');
        jQuery.ajax({
            url: frontendajax.ajaxurl,
            type: 'POST',
            data: {'action':'clickdesigns_get_designs_images_tab_one'},
            success: function(response) {
                var result = JSON.parse(response);
                $(".cd-loader").delay(1).fadeOut("slow");
                if(result.status == 'true'){
                    $('.cd-media-images-ajax').html(result.html);                    
                    if(result.page == 'true'){
                        $('.load_btn').show();
                    } else {
                        $('.load_btn').hide();
                    }
                }
            }
        });
    });

    // Get Designs tab images get 
    $(document).on('click','#cd_get_bundles', function(event){
        $('.cd-loader').show();  
        $('.cd-media-tab a').removeClass('active');
        $(this).addClass('active');
        jQuery.ajax({
            url: frontendajax.ajaxurl,
            type: 'POST',
            data: {'action':'clickdesigns_get_bundles_images_tab_two'},
            success: function(response) {
                var result = JSON.parse(response);
                $(".cd-loader").delay(1).fadeOut("slow");
                if(result.status == 'true'){
                    $('.cd-media-images-ajax').html(result.html);                    
                    if(result.page == 'true'){
                        $('.load_btn').show();
                    } else {
                        $('.load_btn').hide();
                    }
                }
            }
        });
    });

    // Get Designs tab images get 
    $(document).on('click','#cd_get_package', function(event){
        $('.cd-loader').show();
        $('.cd-media-tab a').removeClass('active');
        $(this).addClass('active');
        jQuery.ajax({
            url: frontendajax.ajaxurl,
            type: 'POST',
            data: {'action':'clickdesigns_get_package_images_tab_three'},
            success: function(response) {
                var result = JSON.parse(response);
                $(".cd-loader").delay(1).fadeOut("slow");
                if(result.status == 'true'){
                    $('.cd-media-images-ajax').html(result.html);                    
                    if(result.page == 'true'){
                        $('.load_btn').show();
                    } else {
                        $('.load_btn').hide();
                    }
                }
            }
        });
    });

    // form aaction disable
	$(document).on('submit','.cd-media-tab-search form',function(e){
		e.preventDefault();
    });

    // Search images 
    $(document).on('click','#cd_search_submit', function(event){
        $('.cd-loader').show();
        $(this).addClass('active');
        var keyword = $('#cd_search_field').val();
        var tab = $('.cd-media-tab .active').attr('data-types');
        jQuery.ajax({
            url: frontendajax.ajaxurl,
            type: 'POST',
            data: {'tab': tab, 'keyword': keyword, 'action':'clickdesigns_searchform'},
            success: function(response) {
                var result = JSON.parse(response);
                $(".cd-loader").delay(1).fadeOut("slow");
                if(result.status == 'true'){
                    $('.cd-media-images-ajax').html(result.html);                    
                    if(result.page == 'true'){
                        $('.load_btn').show();
                    } else {
                        $('.load_btn').hide();
                    }
                } else {
                    $('.cd-media-images-ajax').html(result.html);
                    $('.load_btn').hide();
                }
            }
        });
    });

    // Load More
    $(document).on('click','#cd_load_more', function(event){
        $('.cd-loader').show();
        var page = $(this).attr('data-page');
        //var count = $(this).attr('data-count');
        var keyword = $('#cd_search_field').val();
        var tab = $('.cd-media-tab .active').attr('data-types');
        jQuery.ajax({
            url: frontendajax.ajaxurl,
            type: 'POST',
            data: {'page': page,'keyword':keyword,'tab':tab, 'action':'clickdesigns_loadmore'},
            success: function(response) {
                $(".cd-loader").delay(1).fadeOut("slow");
                if(response != ''){
                    $('.cd-media-images-ajax').append(response);
                    var sun = parseInt(page)+1;
                    $('#cd_load_more').attr('data-page',sun);
                } else {
                    $('.load_btn').hide();
                }
            }
        });
    });
    
    // Images Upload Media
    $(document).on('click','.cd-use-thumb', function(event){
        $('.cd-loader').show();
        var image = $(this).attr('data-url');
        var cleanImageUrl = image.split('?')[0];
        var title = $(this).attr('data-id'); 
        var format = $('.cd_images').val(); 
        $.ajax({
            url: frontendajax.ajaxurl,
            type: 'POST',
            data : {'image' : cleanImageUrl, 'format':format, 'title' : title, 'action' : 'clickdesigns_upload_media'}, 
            success: function (response) {		
                var result = JSON.parse(response);						
                if(result.status == 'true'){
                   var test = $("body").find(".media-modal-content").last().find(".media-router .media-menu-item.active").prev().click();
                   wp.media.frame.setState("insert");
                    // refresh 
                    if (wp.media.frame.content.get() !== null) {
                        wp.media.frame.content
                            .get()
                            .collection.props.set({ ignore: +new Date() });
                        let f = wp.media.frame.content.get().collection.select()[0];
                        wp.media.frame.content.get().options.selection.reset(f);
                    } else {
						if(wp.media){
							//wp.media.frame.library.props.set({ ignore: +new Date() });
						}	
                    }
                    setTimeout(() => {
                        let sm = $(
                            ".media-frame-content .attachments-browser .attachments-wrapper ul"
                        )[0];
                       //sm.children[0].click();
                       jQuery(document).find("#menu-item-insert").click(); 
                    }, 1000);
                    $('.cd-loader').hide();
                } else {
                    toastr["error"](result.msg);
                }
            }
        });
    }); 
   

    $(document).on('keypress','#cd_search_field', function(e){
        if(e.which == 13){
            $('.cd-loader').show();
            $(this).addClass('active');
            var keyword = $('#cd_search_field').val();
            var tab = $('.cd-media-tab .active').attr('data-types');
            jQuery.ajax({
                url: frontendajax.ajaxurl,
                type: 'POST',
                data: {'tab': tab, 'keyword': keyword, 'action':'clickdesigns_searchform'},
                success: function(response) {
                    var result = JSON.parse(response);
                    $(".cd-loader").delay(1).fadeOut("slow");
                    if(result.status == 'true'){
                        $('.cd-media-images-ajax').html(result.html);                    
                        if(result.page == 'true'){
                            $('.load_btn').show();
                        } else {
                            $('.load_btn').hide();
                        }
                    } else {
                        $('.cd-media-images-ajax').html(result.html);
                        $('.load_btn').hide();
                    }
                }
            });
        }
    });
	
	// User Image
	$(document).on('change','.cd_user_list', function(e){
        $('.cd-loader').show();
        var user_id = $(this).val();
        var tab = $('.cd-media-tab .active').attr('data-types');
        var keyword = $('.cd-search').val();
        jQuery.ajax({
            url: frontendajax.ajaxurl,
            type: 'POST',
            data: {'tab': tab, 'keyword': keyword, 'user': user_id, 'action':'clickdesigns_user_media'},
            success: function(response) {
                var result = JSON.parse(response);
                $(".cd-loader").delay(1).fadeOut("slow");                
                $('.cd-media-images-ajax').html(result.html); 
                if(result.item > 19){
                    $('#cd_load_more').attr('data-page',1);    
                    $('.load_btn').show();
                } else {
                    $('.load_btn').hide();
                };
            }
        });
    });

    // refresh
	$(document).on('click','.cds_btn_refresh', function(e){
        console.log($(this).parents().find('.media-frame-tab-panel').find('.media-menu-item.active').trigger("click"));
    });
    
    // wp.media load
    jQuery(document).ready(function ($) {
        var l10n = wp.media.view.l10n;
		console.log(l10n);
        wp.media.view.MediaFrame.Select.prototype.browseRouter = function (
            routerView
        ) {
            routerView.set({
                upload: {
                    text: l10n.uploadFilesTitle,
                    priority: 20,
                },
                browse: {
                    text: l10n.mediaLibraryTitle,
                    priority: 40,
                },
                my_tab: {
                    text: "ClickDesigns",
                    priority: 60,
                },
            });
        };
    });
    
});