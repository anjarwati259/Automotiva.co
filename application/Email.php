<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );


class Email extends CI_Controller {

	function __construct() {
		parent::__construct();

		$this->load->model( 'tools/M_email_v2' , 'M_email' );
	}

	function sentMail() {
		$paramArray = array();
		$paramArray[ 'noo_id' ] = $this->input->post( 'noo_id' );
		$paramArray[ 'store_approval_id' ] = $this->input->post( 'store_approval_id' );
		$paramArray[ 'tipe_approval' ] = $this->input->post( 'tipe_approval' );
		$paramArray[ 'depo_id' ] = $this->input->post( 'depo_id' );
		$paramArray[ 'notes' ] = $this->input->post( 'notes' );
		$paramArray[ 'platform' ] = $this->input->post( 'platform' );
		$paramArray[ 'role' ] = $this->input->post( 'role' );
		$paramArray[ 'store_approval_id' ] = $this->input->post( 'store_approval_id' );
		$paramArray[ 'status_type' ] = $this->input->post( 'status_type' );
		// echo json_encode($paramArray); die;

		if($this->input->post( 'noo_id' ) != '0'){
			$this->M_email->sentMail( $paramArray );
		}else{
			$this->M_email->sentMailHawkerApproval( $paramArray );
		}
	}

	function sentMailHawkerApproval() {
		$paramArray = array();
		$paramArray[ 'tipe_approval' ] = $this->input->post( 'tipe_approval' );
		$paramArray[ 'depo_id' ] = $this->input->post( 'depo_id' );
		$paramArray[ 'notes' ] = $this->input->post( 'notes' );
		$paramArray[ 'platform' ] = $this->input->post( 'platform' );
		$paramArray[ 'role' ] = $this->input->post( 'role' );
		$paramArray[ 'store_approval_id' ] = $this->input->post( 'store_approval_id' );
		$paramArray[ 'status_type' ] = $this->input->post( 'status_type' );
		// echo json_encode($paramArray); die;
		$this->M_email->sentMailHawkerApproval( $paramArray );
	}

	function sentMailStoreExit() {
		$paramArray = array();
		$paramArray[ 'store_id' ] = $this->input->post( 'store_id' );
		$paramArray[ 'tipe_approval' ] = $this->input->post( 'tipe_approval' );
		$paramArray[ 'depo_id' ] = $this->input->post( 'depo_id' );
		$paramArray[ 'notes' ] = $this->input->post( 'notes' );
		$paramArray[ 'platform' ] = $this->input->post( 'platform' );
		$paramArray[ 'role' ] = $this->input->post( 'role' );
		// echo json_encode($paramArray); die;

		$this->M_email->sentMailStoreExit( $paramArray );
	}

	function emailNotificationStoreClosed(){
		$paramArray = array();
		$paramArray[ 'store_id' ] = $this->input->post( 'store_id' );

		$this->M_email->emailNotificationStoreClosed( $paramArray );
	}


	function emailSendTest(){

		$emailFrom = $this->input->post('emailFrom');
		$emailAlias = $this->input->post('emailAlias');
		$emailTo = $this->input->post('emailTo');
		$emailCc = $this->input->post('emailCc');

		$mailin = new Mailin("https://api.sendinblue.com/v2.0","2wsSyQEdtV0bWIZ6");
		#"xsmtpsib-20f6c3645e8c83f82640dcb8a32fdf6de9e3192f52aaacf19f9f2affda0f8e1c-EOb3tc6LJAw425vX");
		$data = array(
			"to" => array($emailTo =>"Vito"),
			"from" => array($emailFrom, $emailAlias),
			"subject" => "RESET PASSWORD DIGIFEM",
			"html" => "Your password has been reset to:  <b>adminUnilever2021</b>",
//			"html" => "Your password has been reset By Admin, Please contact Admin for more information.",
			#"attachment" => array("https://domain.com/path-to-file/filename1.pdf", "https://domain.com/path-to-file/filename2.jpg")
		);

		var_dump($mailin->send_email($data));
	}

	function sentMailMutasiDepo() {

        $paramArray = array();
        $paramArray['mutasiId'] = $this->input->post('mutasi_depo_id');
        $paramArray['type'] = $this->input->post('status_type');

        $this->M_email->sentMailMutasiDepo($paramArray);

	}


}

?>
