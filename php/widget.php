<?php
class RESTfulWidget extends WP_Widget {

  function __construct() {
    parent::__construct(
      'restfulwidgets_widget',
      'RESTful Widget',
      array( 'description' => 'A widget to display resources loaded from a RESTful request.' ) 
      );
  }

  public function echoFrontendInput( $type, $name, $displayName ) {
    switch( $type ) {
      case 'text':
      $innerHtml = "<input type=\"text\" class=\"restfulwidgets-parameter-input restfulwidgets-text-input widefat\" name=\"$name\">";
      break;
      case 'integer':
      $innerHtml = "<input type=\"text\" class=\"restfulwidgets-parameter-input restfulwidgets-integer-input widefat\" name=\"$name\">";
      break;
      case 'float':
      $innerHtml = "<input type=\"text\" class=\"restfulwidgets-parameter-input restfulwidgets-float-input widefat\" name=\"$name\">";
      break;
      case 'bool':
      $innerHtml = " <input type=\"checkbox\" class=\"restfulwidgets-parameter-input restfulwidgets-boolean-input widefat\" name=\"$name\"><br>";
      break;
      default:
      $innerHtml = "<p>SOMETHING WENT WRONG HERE<p>";
    }
    echo( "<label>" );
    if ( $displayName ) { echo( $displayName ); } else { echo( $name ); } 
    echo( $innerHtml."</label>" );	
  }

  public function widget( $args, $instance ) {
    wp_enqueue_script( 'restfulwidgets-widget-script' );
    echo( $args['before_widget'] );
    if ( ! empty( $instance['title'] ) ) {
      echo($args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );
    }
    if ( isset( $instance[ 'apiname' ] ) ) {
      $api = $instance[ 'apiname' ];
    } else {
      $api = '';
    }
    if ( isset( $instance[ 'resource_location' ] ) ) {
      $location = $instance[ 'resource_location' ];
    } else {
      $location = '';
    }
    if ( isset( $instance[ 'fields_array' ] ) ) {
      $fields_array = $instance[ 'fields_array' ];
    } else {
      $fields_array = '[]';
    }
    ?>	
    <?php
    $optionsError = false;
    $options = get_option( RESTfulWidgetsSettingsPage::$restfulwidgets_options_name );
    if ($options) {
      if ( isset( $options[ RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id ] ) ) {
        $rows = $options[ RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id ];
        if ( isset( $rows[$api] ) ) {
          $sortablesArray = json_decode( $fields_array );
          $baseurl = $rows[$api];
          ?>	
          <div class="wp-caption">
            <?php if ( count( $sortablesArray ) > 0 ) { ?><p class="wp-caption-text">Parameters:</p></div><?php } ?>
            <form class="restfulwidgets-widget-request-form">
              <?php

              foreach ( $sortablesArray as $name => $data ) {
                $this->echoFrontendInput( $data->type, $name, $data->displayName );
              }
              ?>	
              <input type="submit" class="widefat">
              <input type="hidden" class="restfulwidgets-request-base-url" value="<?php echo( $baseurl ); ?>">
              <input type="hidden" class="restfulwidgets-request-string" value="<?php echo( $location ); ?>">
            </form>
          <div class="restfulwidgets-response-wrapper">
            <div class="wp-caption"><p class="wp-caption-text">Response:</p></div>
          </div>
          <?php
        } else {
          $optionsError = true;
        }
      } else {
        $optionsError = true;
      }
    } else {
      $optionsError = true;
    }
    if ( $optionsError ) {
      echo( '<div class="wp-caption"><p class="wp-caption-text">An API must be selected in the widget options before this widget can be used.</p></div>' );
    }
    echo( $args['after_widget'] );
  }

  public function echoDraggable( $type, $sortable = false, $name = null, $displayName = null ) {
    switch($type) {
      case 'text':
      $descriptionText = 'Text parameter';
      break;
      case 'integer':
      $descriptionText = 'Integer parameter';
      break;
      case 'float':
      $descriptionText = 'Float parameter';
      break;
      case 'bool':
      $descriptionText = 'Boolean parameter';
      break;
      default:
      $descriptionText = '';
    }
    if ( $sortable ) {
      $widgetClass = 'restfulwidgets-parameter-type-sortable';
    } else {
      $widgetClass = 'restfulwidgets-parameter-type-draggable';
    }
    ?>
    <div class="<?php echo( $widgetClass ); ?> widget-title">
      <h3><?php echo( $descriptionText ); ?></h3>
      <input type="hidden" class="restfulwidgets-parameter-type" value="<?php echo( $type ); ?>">
      <div class="<?php if ( !$sortable ) { echo( 'hidden' ); } ?> restfulwidgets-parameter-options">
        <label><p>Name:</p><input type="text" class="restfulwidgets-parameter-name" value="<?php if ( $name ) { echo( $name ); } ?>"></label>
        <label><p>Display Name:</p><input type="text" class="restfulwidgets-parameter-display-name" value="<?php if ( $displayName ) { echo( $displayName ); } ?>"> </label>
      </div>
    </div>
    <?php
  }

  public function form( $instance ) {
    if ( isset( $instance[ 'title' ] ) ) {
      $title = $instance[ 'title' ];
    } else {
      $title = '';
    }
    if ( isset( $instance[ 'apiname' ] ) ) {
      $api = $instance[ 'apiname' ];
    } else {
      $api = '';
    }
    if ( isset( $instance[ 'resource_location' ] ) ) {
      $location = $instance[ 'resource_location' ];
    } else {
      $location = '';
    }
    if ( isset( $instance[ 'fields_array' ] ) ) {
      $fields_array = $instance[ 'fields_array' ];
    } else {
      $fields_array = '[]';
    }
    ?>

    <p>
      <label for="<?php echo( $this->get_field_id( 'title' ) ); ?>">Title:</label> 
      <input class="widefat" id="<?php echo( $this->get_field_id( 'title' ) ); ?>" name="<?php echo( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo( esc_attr( $title ) ); ?>" />
      <label for="<?php echo( $this->get_field_id( 'api' ) ); ?>">API Name:</label> 
      <select class="widefat" id="<?php echo( $this->get_field_id( 'apiname' ) ); ?>" name="<?php echo( $this->get_field_name( 'apiname' ) ); ?>">
        <?php
        $options = get_option( RESTfulWidgetsSettingsPage::$restfulwidgets_options_name );
        if ( $options ) {
          if ( $options[ RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id ] && is_array($options[ RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id ] ) ) {
            $rows = $options[ RESTfulWidgetsSettingsPage::$restfulwidgets_apis_option_id ];
            foreach ( $rows as $name => $url ) {
              echo( '<option value="'.$name.'"'.selected( $api, $name ).'>'.$name.'</option>' );
            }
          }
        }
        ?>
      </select>	
    </p>

    <div class="restfulwidgets-widget-options-section restfulwidgets-request-parameter-types-list widgets-holder-wrap">
      <div class="restfulwidgets-widget-options-section-header">
        <div class="restfulwidgets-widget-options-section-name">
          <h4> Request parameter types </h4>
        </div>
        <div class="restfulwidgets-widget-options-section-description">
          <p> Drag a type of parameter below to add a new parameter to the widget. The name of the parameter should match the name in your query string. </p>
        </div>
      </div>
      <?php
      $this->echoDraggable( 'text' );
      $this->echoDraggable( 'integer' );
      $this->echoDraggable( 'float' );
      $this->echoDraggable( 'bool' );
      ?>
    </div>

    <div class="restfulwidgets-widget-options-section restfulwidgets-request-widget-parameters-list widgets-holder-wrap">
      <div class="restfulwidgets-widget-options-section-header">
        <div class="restfulwidgets-widget-options-section-name">
          <h4> Request parameters </h4>
        </div>
        <div class="restfulwidgets-widget-options-section-description">
          <p> Describe the location of the resource relative to the chosen Base URL.
            The specififed names of the parameters will be used to form the query string, and appended to the resource location,
            ie. a parameter with name 'maximum' will be appended to the location as '?maximum=<span style="font-style: italic;">user input</span>'. The display name is an optional field which will be used to label the field as an alternative to the parameter name.</p>
        </div>
      </div>
      <div class="restfulwidgets-widget-options-form">
        <label for="<?php echo( $this->get_field_id( 'resource_location' ) ); ?>">Resource Location:</label>
        <div class="restfulwidgets-input-error-notification restfulwidgets-naming-error">Every field must have a unique non empty name.</div>
        <input type="text" class="restfulwidgets-resource-location-input widefat" id="<?php echo( $this->get_field_id( 'resource_location' ) ); ?>" name="<?php echo( $this->get_field_name( 'resource_location' ) ); ?>" value="<?php echo( esc_attr( $location ) ); ?>" />
      </div> 
      <?php
      $sortablesArray = json_decode( $fields_array );
      foreach ( $sortablesArray as $name => $data ) {
        $this->echoDraggable( $data->type, true, $name, $data->displayName );
      }
      ?>
    </div>
    <input type="hidden" class="restfulwidgets-request-parameters-array" name="<?php echo( $this->get_field_name( 'fields_array' ) ); ?>" value="<?php echo( esc_attr( $fields_array ) ); ?>">
    <?php 
  }

  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['apiname'] = ( ! empty( $new_instance['apiname'] ) ) ? strip_tags( $new_instance['apiname'] ) : '';
    $instance['resource_location'] = ( ! empty( $new_instance['resource_location'] ) ) ? strip_tags( $new_instance['resource_location'] ) : '';
    $instance['fields_array'] = ( ! empty( $new_instance['fields_array'] ) ) ? strip_tags( $new_instance['fields_array'] ) : '';
    return $instance;
  }
}

add_action( 'widgets_init', function() {
  register_widget( 'RESTfulWidget' );
});