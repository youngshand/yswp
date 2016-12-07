<?php

// 
function ys_login_logo() {
  ?>
    <style type="text/css">
        #login h1 a {
          width: 204px;
          background-image: url('');
          background-size: 100%;
        }
        #backtoblog{
          display: none;
        }
        .login{
          # background-image: url('');
        }
      }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'ys_login_logo' );

/* Remove dashboard widgets */
add_action('admin_init', function() {

  remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
  remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
  remove_meta_box('dashboard_primary', 'dashboard', 'side');
  remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
  remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
  remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
  remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
  remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
  remove_meta_box('dashboard_activity', 'dashboard', 'normal');
  remove_action( 'welcome_panel', 'wp_welcome_panel' );

  // Remove update notice
  remove_action('admin_notices', 'update_nag', 3);
  // Remove version from footer
  remove_filter('update_footer', 'core_update_footer');

});

/* Remove help tab */
add_action('admin_head', function() {
  $screen = get_current_screen();
  $screen->remove_help_tabs();
});

/* Remove screen options tab */
add_filter('screen_options_show_screen', '__return_false');

/* Customise admin bar */
add_action('admin_bar_menu', function($wp_admin_bar) {

  // Remove menu items
  $wp_admin_bar->remove_menu('wp-logo');
  $wp_admin_bar->remove_node('comments');
  $wp_admin_bar->remove_node('new-content');
  $wp_admin_bar->remove_node('updates');
  $wp_admin_bar->remove_node('update-nag');
  $wp_admin_bar->remove_node( 'welcome-panel' );

  // Alter text
  $account = $wp_admin_bar->get_node('my-account');
  $newtitle = str_replace('Kia ora,', 'Hello,', $account->title);
  $wp_admin_bar->add_node(array(
    'id' => 'my-account',
    'title' => $newtitle,
  ));

}, 999);

/* Change the dashboard footer text */
add_filter('admin_footer_text', function() {
  echo '';
});

/* Remove menu items */
add_action('admin_menu', function() {

  remove_menu_page( 'edit.php' );                   //Posts
  remove_menu_page( 'edit-comments.php' );          //Comments

  if (!current_user_can('manage_options')) {
    // remove_menu_page( 'index.php' );                  //Dashboard
    // remove_menu_page( 'edit.php' );                   //Posts
    // remove_menu_page( 'upload.php' );                 //Media
    // remove_menu_page( 'edit.php?post_type=page' );    //Pages
    remove_menu_page( 'edit-comments.php' );          //Comments
    // remove_menu_page( 'themes.php' );                 //Appearance
    remove_menu_page( 'plugins.php' );                //Plugins
    // remove_menu_page( 'users.php' );                  //Users
    remove_menu_page('tools.php');                       //Tools
    // remove_menu_page( 'options-general.php' );        //Settings
    
    add_filter('acf/settings/show_admin', '__return_false');  /* Hide ACF */
  }
});

// Removing WYSIWYG editor from page post types
add_action('init', 'init_remove_support',100);
function init_remove_support(){
    $post_type = 'page';
    remove_post_type_support( $post_type, 'editor');
    remove_post_type_support( $post_type, 'comments');
    remove_post_type_support( $post_type, 'revisions');
}
