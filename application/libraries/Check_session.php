<?php

class Check_session {

	function check() {
		$CI =& get_instance();
		if ( is_null( $CI->session->userdata( 'id_user' ) ) ||
			is_null( $CI->session->userdata( 'role_id' ) ) ||
			is_null( $CI->session->userdata( 'depo_id' ) ) ||
			is_null( $CI->session->userdata( 'team_id' ) ) ||
			is_null( $CI->session->userdata( 'product_group_id' ) ) ||
			is_null( $CI->session->userdata( 'fullname' ) ) ) {

			return FALSE;
		} else {
			return TRUE;
		}

	}

	function check_access( $role ) {
		$CI =& get_instance();
		if ( !in_array( $CI->session->userdata( 'role_id' ) , $role ) ) {
			$CI->config->set_item( 'page_title' , 'Forbidden Access' );
			$CI->config->set_item( 'content_title' , 'Forbidden Access' );
			$CI->load->view( 'errors/v_403' );

		}
	}

}

?>
