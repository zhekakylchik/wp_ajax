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
<script>
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
</script>

<?php
//  Ajax Login
  function ajax_login_init(){
    
	/* Connecting script for authorization */
	wp_enqueue_script('script', get_template_directory_uri() . '/js/jquery.min.js');
    wp_register_script('ajax-login-script', get_template_directory_uri() . '/js/ajax-login-script.js'); 
    wp_enqueue_script('ajax-login-script');
    
	/* Localizing script parameters */
    wp_localize_script( 'ajax-login-script', 'ajax_login_object', array( 
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
      'redirecturl' => $_SERVER['REQUEST_URI'],
      'loadingmessage' => __('Checking data, wait a second...')
    ));

    // Allow users without privileges to run the ajax_login() function
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
  }

  // Perform authorization only if the user is not logged in
  if (!is_user_logged_in()) {
    add_action('init', 'ajax_login_init');
  }

  function ajax_login(){

    // First of all, checking the security setting
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Getting data from form fields and validating them
    $args = array(
		'orderby'      => 'email',
		'order'        => 'ASC',
		'search'       => $_POST['useremail'],
		'search_columns' => array('email'),
	);
    $users = get_users( $args );

    $info = array();
    $info['user_login'] = ($users)?$users[0]->data->user_login:null;
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
      echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong email or password!')));
    } else {
      echo json_encode(array('loggedin'=>true, 'message'=>__('Excellent! Redirect in progress...')));
    }

    die();
  }
?>
