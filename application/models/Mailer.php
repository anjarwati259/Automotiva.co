<?php defined('BASEPATH') or exit('No direct script access allowed');

// require_once(__DIR__ . '/vendor/autoload.php');

class Mailer extends CI_Model
{
  public function insertEmail($paramemail)
  {
    $emailkiriman = $paramemail;
    $data = [];
    $data['email'] = $emailkiriman;
    $data['date_time']  =  date('Y-m-d H:i:s');
    $this->db->insert('email', $data);
    // Produces: INSERT INTO mytable (title, name, date) VALUES ('My title', 'My name', 'My date')
  }

  public function emailSendSchedule($paramemail)
  {
    $emailFrom = 'test@test123123.com';
    $emailAlias = 'test@testwillycom';
    $notes = '';
    $tanggal = '';
    $tanggal = '2022-10-17';
    $notes = 'test';

    $base_url = 'transaction/auto_pk_reguler/submit_add';
    $base_url_img = '/assets/img/emailbackground.png';


    $ho[$paramemail] = 'willy';

    $emailTo = $ho;
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
    $str .= "<td><b><a href='$base_url_img'>CONFIRM</a></b></td>";
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
    $mailin = new Mailin("https://api.sendinblue.com/v2.0", "2wsSyQEdtV0bWIZ6");
    $data_email['to'] = $emailTo;

    $data_email['from'] = array("automotiva.co", "Automotiva.co");
    $data_email['subject'] = $emailSubject;
    $data_email['html'] = $emailBody;
    $data_email['headers'] = array("Content-Type" => "text/html; charset=iso-8859-1");


    $res = $mailin->send_email($data_email);

    //   print_r($data_email);die;
    $send = $res["code"] == "success";

    return ($send) ? "SUCCESS" : "FAILED";
  }
}
