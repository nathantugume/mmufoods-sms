<?php
/**
 * Plugin Name: mmufoods-sms
 * Plugin URI: https://github.com/nathantugume/mmufoods-sms/
 * Author: Nathan
 * Author URI: https://github.com/nathantugume/mmufoods-sms/
 * Description: Send SMS notifications from woocommerce Order notifications upon status change.
 * Version: 0.0.1
 */
  // Event of a new email for the order - change in status
  add_action( 'woocommerce_order_status_changed', 'mmufoods_send_sms_on_new_order_status', 10, 4 );
 
  // Event of the order note
  add_action( 'woocommerce_new_customer_note_notification', 'mmufoods_send_sms_on_new_order_notes', 10, 1 );
  
  
  function mmufoods_send_sms_on_new_order_status( $order_id, $old_status, $new_status, $order  ) {
      // Get the order Object
      $my_order = wc_get_order( $order_id );
      
      $firstname = $my_order->get_billing_first_name(); // firstname
      $phone     = $my_order->get_billing_phone(); // Phone
      $shopname  = get_option( 'woocommerce_email_from_name');
      
      $default_sms_message = "Thank you $firstname for shopping with $shopname. Your Order #$orderid is $new_status";
      
      mmufoods_send_sms_to_customer( $phone, $default_sms_message, $shopname );
      
  }
  
  function mmufoods_send_sms_on_new_order_notes( $email_args ) {
      
      $order = wc_get_order( $email_args['order_id'] );
      $note  = $email_args['customer_note'];
      
      $phone     = $order->get_billing_phone(); // Phone
      $shopname  = get_option( 'woocommerce_email_from_name');
      
      mmufoods_send_sms_to_customer( $phone, $note, $shopname );
  }
  
  function mmufoods_send_sms_to_customer( $phone = 'NULL', $default_sms_message, $shopname ) {
     
     if ( 'NULL' === $phone ) {
         return;
     }
     
     $msgdata = array(
         'method' => 'SendSms',
         'userdata' => array(
             'username' => 'nathan',
             'password' => 'KZqBx7rd6DZZs4e',
         ),
         'msgdata' => array(
             array(
                 'number' => $phone,
                 'message' => $default_sms_message,
                 'senderid' => $shopname,
             )
         )
     );
     
     $url = 'http://sms.sukumasms.com/api/v1/json/';
 
     $arguments = array(
         'method' => 'POST',
         'body' => json_encode( $msgdata ),
     );
 
     $response = wp_remote_post( $url, $arguments );
     
     if ( is_wp_error( $response ) ) {
         $error_message = $response->get_error_message();
         return "Something went wrong: $error_message";
     }
      
  }
