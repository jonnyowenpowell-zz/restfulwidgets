<?php
class RESTfulWidgetsIncludes {

  function __construct() {
    $this->style_url = plugin_dir_url( __FILE__ ) . './styles/style.css';
    $this->options_js_url = plugin_dir_url( __FILE__ ) . './js/options.js';
    $this->widget_options_js_url = plugin_dir_url( __FILE__ ) . './js/widget_options.js';
    $this->frontend_js_url = plugin_dir_url( __FILE__ ) . './js/frontend.js';

    $this->style_version = date( "ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . './styles/style.css' ) );
    $this->options_js_version = date( "ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . './js/options.js' ) );
    $this->widget_options_js_version = date( "ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . './js/widget_options.js' ) );
    $this->frontend_js_version = date( "ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . './js/frontend.js' ) );

    add_action( 'admin_enqueue_scripts', array( $this, 'admin_includes' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'front_end_includes' ) );
  }

  function admin_includes( $page ) {
    wp_register_style( 'restfulwidgets-style', $this->style_url, $this->style_version );
    wp_register_script( 'restfulwidgets-options-js', $this->options_js_url , array( 'jquery' ), $this->options_js_version );
    wp_register_script( 'restfulwidgets-widget-options-js',  $this->widget_options_js_url, array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable' ), $this->widget_options_js_version );
    if ( 'settings_page_restfulwidgets-settings' == $page ) {
      wp_enqueue_style( 'restfulwidgets-style' );
      wp_enqueue_script( 'restfulwidgets-options-js' );
    }
    if ( 'widgets.php' == $page ) {
      wp_enqueue_style( 'restfulwidgets-style' );
      wp_enqueue_script( 'restfulwidgets-widget-options-js' );
    }
  }

  function front_end_includes( $page ) {
    global $wp_scripts;
    $jquery_ui_style_url = 'http://ajax.googleapis.com/ajax/libs/jqueryui/' . $wp_scripts->query( 'jquery-ui-core' )->ver . '/themes/smoothness/jquery-ui.css';
    wp_register_style( 'restfulwidgets-style', $this->style_url, $this->style_version );
    wp_register_script( 'restfulwidgets-frontend-js',  $this->frontend_js_url, array( 'jquery', 'jquery-ui-slider' ), $this->frontend_js_version );
    wp_enqueue_style( 'jquery-ui-smoothness', $jquery_ui_style_url );
    wp_enqueue_style( 'restfulwidgets-style' );
    wp_enqueue_script( 'restfulwidgets-frontend-js' );
  }

}

$restfulwidgets_includes = new RESTfulWidgetsIncludes();