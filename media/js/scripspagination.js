jQuery(document).ready(function($){

                jQuery('.itemsPagination').map(function()
                	{ 
                    	return jQuery(this).attr("id"); 
                	}).each(function()
                		{
                			jQuery("#".concat(this)).pajinate({

                				items_per_page : 20,
                				num_page_links_to_display : 5,
                				nav_label_first : 'Primero',
								nav_label_last : 'Último',
								nav_label_prev : '<',
								nav_label_next : '>'

                			});
                		});
                jQuery('.itemsPagination').map(
                function()
                { 
                    return  jQuery(this).attr("id");;
                }).each(
                    function()
                    {   var idPagination = "#".concat (this);
                        jQuery(idPagination.concat(' .page_navigation')).click(
                            function()
                            {
                                jQuery('body').animate({scrollTop : jQuery(idPagination).position().top *0.95 } , 500);
                                return false;
                            });
                    });

            });


