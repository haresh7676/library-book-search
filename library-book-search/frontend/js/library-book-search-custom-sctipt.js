(function( $ ) {
	$(window).bind("load", function() {
	    $( ".slider-range" ).slider({
	      range: true,
	      min: 0,
	      max: 3000,
	      values: [ 0, 3000 ],
	      slide: function( event, ui ) {
	      	var endprice = '';
	      	if(ui.values[ 1 ] == 3000) {
	      		endprice = '+';
	      	}
	        $( ".amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ]+endprice);
	      }
	    });
	    $( ".amount" ).val( "$" + $( ".slider-range" ).slider( "values", 0 ) +
	      " - $" + $( ".slider-range" ).slider( "values", 1 ) + '+');
	    $( ".selectmenu-dropdown" ).selectmenu();

	    function lbs_load_all_posts(page, sorting_datas){                
                $(".lbs_universal_container").html('<p><img src = "'+lbs_ajax_object.plugin_dirurl+'/images/loading.gif" class = "loader" /></p>');
                               
                var post_data = {
                    page: page,                    
                    sorting_data: sorting_datas
                };
               
                var data = {
                    action: "load_my_books",
                    data: post_data
                };
               
                $.post(lbs_ajax_object.ajaxurl, data, function(response) {
                    if($(".lbs_universal_container").html(response)){
                        $('.lbs_universal_container .lbs-universal-pagination li.active').on('click',function(){
                            var page = $(this).attr('p');
                            var filter_date = $('input.inputfilter').val();      
                            lbs_load_all_posts(page, filter_date);   
                        });
                    }
                });
            }

            $(".sorting").submit(function(e) {
                var filter_date = $(".sorting").serialize();   
                $('input.inputfilter').val(filter_date);
                lbs_load_all_posts(1, filter_date);                                  
                e.preventDefault(); // avoid to execute the actual submit of the form.
            })
            var filter_date = $('input.inputfilter').val();   
            lbs_load_all_posts(1, filter_date);   
	})
})( jQuery );
  