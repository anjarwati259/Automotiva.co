<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Main extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Mailer', 'mailer');
	}
	public function index()
	{
		$data = array(
			'title' => 'Dashboard',
			'isi' => 'main/index'
		);
		$this->load->view('layout/wrapper', $data, FALSE);
	}

	// controller mailer
	public function mailer()
	{
		$paramemail = $this->input->post('email');
		echo $paramemail;
		$this->mailer->insertEmail($paramemail);
		// $this->mailer->emailSendSchedule($paramemail);
		$this->sendEmail($paramemail);
	}

	public function sendEmail($email)
	{
		$emailSubject = 'Submitting by Email Confirmation';

		$str = '';
		$str .= '<table style="width="100%;border:none;">';
		$str .= '<tr>';
		$str .= '<img src="https://i.ibb.co/M9PyKJw/emailbackground.png" alt="" width="100%">';
		$str .= '</tr>';
		$str .= '<tr>';
		$str .= '<td><h4>Thank you submitting to Automotiva Co . Please click the link below to validate the email address.</h4></td>';
		$str .= '</tr>';
		$str .= '<tr>';
		$str .= "<td><b>CONFIRM</b></td>";
		$str .= '</tr>';
		$str .= '<br>';
		$str .= '<tr>';
		$str .= "<td>Thanks.</td>";
		$str .= '</tr>';
		$str .= '<tr>';
		$str .= "<td>The Automotiva Co Team</td>";
		$str .= '</tr>';
		$str .= '<br>';
		$str .= '<br>';
		$str .= '<tr>';
		$str .= '<td>[Bussines Name]</td>';
		$str .= '</tr>';
		$str .= '<tr>';
		$str .= '<td>[Address]</td>';
		$str .= '</tr>';
		$str .= '<tr>';
		$str .= '<td>[Phone]</td>';
		$str .= '</tr>';
		$str .= '<tr>';
		$str .= '</tr>';
		$str .= '</table>';

		$emailBody = $str;

		$curl_data = array(
			"email_sender" => ["name" => "Automotiva", "email" => "automotiva@gmail.com"],
			"email_replyto" => ["name" => "Automotiva", "email" => "automotiva@gmail.com"],
			"email_tos" => [["email" => $email, "name" => "l"]],
			"email_subject" => $emailSubject,
			"email_body" => $emailBody,

		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://email-api.pitjarus.co/send_mail');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = json_decode(curl_exec($ch));
		curl_close($ch);
		print_r(json_encode($curl_data));
		print_r($output);
	}


	public function sender_email()
	{
	}
}
