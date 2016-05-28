<?php

if( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class API_URL_List_Table extends WP_List_Table {

  function __construct( $option_name, $table_option_item_name ) {
    global $status, $page;
    parent::__construct(
      array(
        'singular'  => 'RESTful API',
        'plural'    => 'RESTful APIs'
        )
      );
    $this->_args['option_name'] = $option_name;
    $this->_args['table_option_item_name'] = $table_option_item_name;
  }

  function get_data() {
    $options = get_option( $this->_args['option_name'] );
    if (!$options) {
      $rows = array();
    } else {
      if ( $options[$this->_args['table_option_item_name']] ) {
        $rows = $options[$this->_args['table_option_item_name']];
      } else {
        $rows = array();
      }
    }
    $rows[''] = '';
    return $rows;
  }

  function get_columns() {
    $columns = array(
      'name' => 'Unique Name',
      'url'    => 'API Root URL'
      );
    return $columns;
  }

  function column_name( $item ) {
    $class = 'restfulwidgets_name_input';
    if ( isset( get_option( $this->_args['option_name'] )[$this->_args['table_option_item_name']] ) ) {
      $name =  array_search( $item, get_option($this->_args['option_name'] )[$this->_args['table_option_item_name']]);
    } else {
      $name = '';
    }
    $actions = array(
      'delete'    => sprintf('<a href="#" class="restfulwidgets_delete_row">Delete</a>')
      );
    $actions_html = '';
    if ( ! empty($name) ) {
      $actions_html = $this->row_actions( $actions );
    }
    return sprintf( '<input type="text" placeholder="Unique API Name" class="%1$s" value="%2$s" rowName="%2$s" /> %3$s', $class, $name, $actions_html );    
  }

  function column_url( $item ) {
    $class = 'restfulwidgets_url_input';
    if ( isset( get_option( $this->_args['option_name'] )[$this->_args['table_option_item_name']] ) ) {
      $name =  array_search( $item, get_option( $this->_args['option_name'] )[$this->_args['table_option_item_name']] );
    } else {
      $name = '';
    }
    return sprintf( '<input type="text" placeholder="API URL" class="%1$s" name="%2$s[%3$s][restfulwidgetsrowname]" value="%5$s" />', $class, $this->_args['option_name'], $this->_args['table_option_item_name'], $name, $item );
  }


  function column_default( $item, $column_name ) {
    switch( $column_name ) { 
      case 'name':
      case 'url':
      return $item[ $column_name ];
      default:
      return print_r( $item, true ) ;
    }
  }

  function ajax_response() {
    check_ajax_referer( 'restfulwidgets-ajax-api-table-nonce', '_restfulwidgets_ajax_api_table_nonce' );
    if ( ! empty( $_REQUEST['deletedRow'] ) ) {
      $deletedRow = $_REQUEST['deletedRow'];
      if ( is_string( $deletedRow ) ) {
        $options = get_option( $this->_args['option_name'] );
        if ( $options ) {
          if ( $options[$this->_args['table_option_item_name']] ) {
            $rows = $options[$this->_args['table_option_item_name']];
            unset( $rows[$deletedRow] );
            $options[$this->_args['table_option_item_name']] = $rows;
            update_option( $this->_args['option_name'], $options );
          }
        }
      }
    }
    $this->prepare_items();
    extract( $this->_args );
    extract( $this->_pagination_args, EXTR_SKIP );
    ob_start();
    $this->display_rows_or_placeholder();
    $response = ob_get_clean();
    die( json_encode( $response ) );
  }

  function display() {
    wp_nonce_field( 'restfulwidgets-ajax-api-table-nonce', '_restfulwidgets_ajax_api_table_nonce' );
    parent::display();
  }

  function prepare_items() {
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = array();
    $this->_column_headers = array( $columns, $hidden, $sortable );
    $this->items = $this->get_data();
  }

}

?>