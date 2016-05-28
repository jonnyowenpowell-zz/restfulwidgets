<?php

include( plugin_dir_path( __FILE__ ) . 'options_table.php');

class RESTfulWidgetsSettingsPage {

  public static $restfulwidgets_options_page_name = 'restfulwidgets-settings';
  public static $restfulwidgets_options_name = 'restfulwidgets_options';
  public static $restfulwidgets_options_group_name = 'restfulwidgets_options_group';
  public static $restfulwidgets_form_id = 'restfulwidgets-settings-form';
  public static $restfulwidgets_options_table_id = 'restfulwidgets-options-table';
  public static $restfulwidgets_options_section_id = 'restfulwidgets_restful_api_urls_section';
  public static $restfulwidgets_apis_option_id = 'restfulwidgets_restful_apis';

  /**
  * Holds the values to be used in the fields callbacks
  */
  private $options;

  /**
  * Initialize
  */
  public function __construct() {
    add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    add_action( 'admin_init', array( $this, 'page_init' ) );
  }

  /**
  * Add options page
  */
  public function add_plugin_page() {
    add_options_page(
      'RESTful Widgets Settings', 
      'RESTful Widgets', 
      'manage_options', 
      RESTfulWidgetsSettingsPage::$restfulwidgets_options_page_name, 
      array( $this, 'create_admin_page' )
      );
  }

  /**
  * Options page callback
  */
  public function create_admin_page() {
    $this->options = get_option( RESTfulWidgetsSettingsPage::$restfulwidgets_options_name );
    ?>
    <div class="wrap">
      <h2>RESTful Widgets Settings</h2>
      <h2>RESTful API URLs</h2>
      Enter the URLs for each API to be used in a RESTful Widget below: <div class="restfulwidgets-input-error-notification">Every URL must have a unique name (case insensitive).</div>
      <?php echo( '<form method="post" action="options.php" id="'.RESTfulWidgetsSettingsPage::$restfulwidgets_form_id.'">' ); ?>
        <div id="restfulwidgets-options-table">
          <?php
            $options_table = new API_URL_List_Table( RESTfulWidgetsSettingsPage::$restfulwidgets_options_name, RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id ); 
            $options_table->prepare_items();
            $options_table->display();
          ?>
        </div>
        <?php
          settings_fields( RESTfulWidgetsSettingsPage::$restfulwidgets_options_group_name );
          submit_button(); 
        ?>
      </form>
    </div>
  <?php
  }

  /**
  * Register and add settings
  */
  public function page_init() {   
    register_setting(
      RESTfulWidgetsSettingsPage::$restfulwidgets_options_group_name,
      RESTfulWidgetsSettingsPage::$restfulwidgets_options_name,
      array( $this, 'sanitize' )
      );

    add_settings_section(
      RESTfulWidgetsSettingsPage::$restfulwidgets_options_section_id,
      'RESTful API URLs',
      array( $this, 'print_apis_section_info' ),
      RESTfulWidgetsSettingsPage::$restfulwidgets_options_page_name
      );  

    add_settings_field(
      RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id,
      'restful_apis',
      array( $this, 'restful_apis_callback' ),
      RESTfulWidgetsSettingsPage::$restfulwidgets_options_page_name,
      RESTfulWidgetsSettingsPage::$restfulwidgets_options_section_id          
      );
  }

  /**
  * Sanitize each setting field as needed
  *
  * @param array $input Contains all settings fields as array keys
  */
  public function sanitize( $input ) {
    $new_input = array();
    if( is_array( $input[RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id] ) ) {
      $new_input[RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id] = array();
      $rowarray = $input[RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id];
      foreach ( $rowarray as $rowName => $rowUrl ) {
        $name = sanitize_text_field( $rowName );
        $url = esc_url( $rowUrl, array('http') );
        if ( ! empty( $name ) and ! empty( $url ) ) {
          $new_input[RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id][$name] = $url;
        }
      }
    }
    return $new_input;
  }

  public function print_apis_section_info() {
    // Handled elsewhere.
  }
  
  public function restful_apis_callback() {
    // Handled elsewhere.
  }

}

function _restfulwidgets_ajax_update_api_table_callback() {
  $options_table = new API_URL_List_Table( RESTfulWidgetsSettingsPage::$restfulwidgets_options_name, RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id );
  $options_table->ajax_response();
}

add_action( 'wp_ajax__restfulwidgets_ajax_update_api_table', '_restfulwidgets_ajax_update_api_table_callback' );

if( is_admin() ) {
  $restfulwidgets_settings_page = new RESTfulWidgetsSettingsPage();
}