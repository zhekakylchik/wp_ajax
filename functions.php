<?php
  add_action( 'wp_ajax_do_something',        'get_posts_callback' ); // For logged in users
add_action( 'wp_ajax_nopriv_do_something', 'get_posts_callback' );
function get_posts_callback(){
    echo(json_encode( array('status'=>'ok','request_vars'=>$_REQUEST) ));
    wp_die();
}

function js_variables(){
    $variables = array (
        'ajax_url' => admin_url('admin-ajax.php'),
    	'is_mobile' => wp_is_mobile()
    );
    echo(
        '<script type="text/javascript">window.wp_data = '.
        json_encode($variables).
        ';</script>'
    );
}
add_action('wp_head','js_variables');
?>

$(document).ready(function(){
					$(document).click(function(){
					$.ajax({
		    		    type: "GET",
		    		    url: window.wp_data.ajax_url,
		    		    data: {
		    		        action : 'do_something'
		    		    },
		    		    success: function (response) {
		    		        console.log('AJAX response : ',response);
		    		    }
		    		});
				});
				});
