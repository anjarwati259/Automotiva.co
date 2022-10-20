<?php

class M_email_v2 extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('email');
        #$this->load->library("mailin");
    }

    public function emailNotificationStoreClosed($paramArray)
    {
        $return_array = array();
        $return_data = array();

        $return_array['store'] = $this->getDataStoreClosed($paramArray);

        $depo_id = $return_array['store']['depo_id'];

        $return_array['kadep'] = $this->getKadepEmailStoreClosed($depo_id);

        $return_array['cc'] = $this->getCcEmailStoreClosed($depo_id);

        $sendEmailDataStoreClosed = $this->sendEmailStoreClosed($return_array, $paramArray);

        echo json_encode($sendEmailDataStoreClosed);
    }

    private function sendEmailStoreClosed($data, $paramArray)
    {

        $nama_toko = $data['store']['nama_toko'];
        $kode_toko = $data['store']['kode_toko'];
        $wkt_kunjungan = $data['store']['wkt_kunjungan'];
        $reason = $data['store']['reason'];
        $nik_user = $data['store']['nik_user'];
        $name_user = $data['store']['name_user'];
        $photo_path = $data['store']['foto'];
        $base_url = base_url();
        $base_url_photo = 'http://dev.pitjarus.co/api/sariroti/dms/';
        //        $base_url_photo = 'https://dms-mainapi.pitjarus.co/';

        $emailTo = array_merge($data['kadep'], $data['cc']);
        //        $emailCC = $data['cc'];
        $emailCC = null;

        $str = '';
        $str .= '<table style="width="100%;border:none;">';
        $str .= '<tr>';
        $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">STORE CLOSED</td>';
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= '<td><b><h4>Yth. KADEP, RSM, ASM/ASS, SCM, ROUTE PLAN</h4></b></td>';
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>Berikut di informasikan Outlet Closed dengan Detail.</td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= '<table>';
        $str .= '<tr>';
        $str .= "<td>Nama Toko</td><td>:<b> $nama_toko </b></td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>Kode Toko</td><td>:<b> $kode_toko </b> </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>Waktu Kunjungan</td><td>:<b> $wkt_kunjungan </b></td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>Deliveryman</td><td>:<b> ($nik_user) $name_user </b></td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>Alasan</td><td>:<b> $reason </b><br></td>";
        $str .= '</tr>';
        $str .= '</table>';
        $str .= '</tr>';
        $str .= '<tr>';
        //        $str .= "<td>FOTO STORE. <form action='$base_url"."$photo_path'><button style='background-color:#20335C;color:white;cursor:pointer;font-size: 20px;border-radius: 4px;box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);'>Link Foto</button></form><br></td>";
        $str .= "<td>FOTO STORE. <b><a href='$base_url_photo" . "$photo_path'>Link</a></b><br></td>";
        $str .= '</tr>';
        $str .= '<tr>';
        //        $str .= '<td><b><p>Best Regards,</p><p>ASS/ASM</p></b></td>';
        $str .= '<td></td>';
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url . 'asset/images/logoBrowser.png" /></td>';
        $str .= '</tr>';
        $str .= '</table>';

        $emailSubject = "($kode_toko)$nama_toko CLOSSED CONFIRMATION";
        $emailBody = $str;

        if ($emailTo != null) {
            $mailin = new Mailin("https://api.sendinblue.com/v2.0", "2wsSyQEdtV0bWIZ6");
            $data_email['to'] = $emailTo;
            if ($emailCC != null) {
                $data_email['cc'] = $emailCC;
            }
            $data_email['from'] = array("dms-system@pitjarus.co", "DMS SYSTEM");
            $data_email['subject'] = $emailSubject;
            $data_email['html'] = $emailBody;
            $data_email['headers'] = array("Content-Type" => "text/html; charset=iso-8859-1");


            $res = $mailin->send_email($data_email);

            $send = $res["code"] == "success";

            return ($send) ? "SUCCESS" : "FAILED";
        } else {
            return 'FAILED';
        }
    }

    private function getDataStoreClosed($paramArray)
    {
        $this->db->select('str.store_code as kode');
        $this->db->select('str.store_name as nama');
        $this->db->select('sc.created as wkt_kunjungan');
        $this->db->select('sc.reason as reason');
        $this->db->select('sc.photo_path as foto');
        $this->db->select('ul.nik as nik_user');
        $this->db->select('ul.name as name_user');
        $this->db->select('str.depo_id as depo_id');
        $this->db->from('store_closed sc');
        $this->db->join('store str', 'str.store_id = sc.store_id');
        $this->db->join('user_login ul', 'ul.user_id = sc.user_id');
        $this->db->where('sc.store_id', $paramArray['store_id']);

        $result = $this->db->get();

        if ($result->num_rows() < 1)
            return null;

        $result = $result->result();

        $return_arr = array();

        foreach ($result as $key => $value) {
            $return_arr['kode_toko'] = $value->kode;
            $return_arr['nama_toko'] = $value->nama;
            $return_arr['wkt_kunjungan'] = $value->wkt_kunjungan;
            $return_arr['reason'] = $value->reason;
            $return_arr['nik_user'] = $value->nik_user;
            $return_arr['name_user'] = $value->name_user;
            $return_arr['depo_id'] = $value->depo_id;
            $return_arr['foto'] = $value->foto;
        }

        return $return_arr;
    }

    private function getKadepEmailStoreClosed($depo_id)
    {
        $this->db->select('wu.email as email, wu.fullname as name');
        $this->db->from('web_depo_coverage wdc');
        $this->db->join('web_user wu', 'wu.id_web_user = wdc.id_web_user');
        $this->db->where('wdc.depo_id', $depo_id);
        $this->db->where("wu.email is not null", null, false);
        $this->db->where("wu.role_id in (3,22,18,19)", null, false);

        $result = $this->db->get();

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->result();

        foreach ($result as $key => $value) {
            $return_arr[$value->email] = $value->name;
        }

        return $return_arr;
    }

    private function getCcEmailStoreClosed($depo_id)
    {
        $return_arr = array();


        $this->db->select('wu.email as email, wu.fullname as name');
        $this->db->from('web_depo_coverage wdc');
        $this->db->join('web_user wu', 'wu.id_web_user = wdc.id_web_user');
        $this->db->where('wdc.depo_id', $depo_id);
        $this->db->where_in('wu.role_id', array('7', '8', '12'));
        $this->db->where("wu.email is not null", null, false);
        $this->db->order_by('wu.role_id', 'ASC');

        $result = $this->db->get();

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->result();

        foreach ($result as $key => $value) {
            $return_arr[$value->email] = $value->name;
        }

        return $return_arr;
    }

    public function sentMail($paramArray)
    {
        $return_array = array();
        $return_data = array();

        $return_array['store'] = $this->getDataStore($paramArray);
        $return_array['ho'] = $this->getDataHo($paramArray);
        $return_array['ho_cc'] = $this->getDataHocc($paramArray);
        $return_array['assm'] = $this->getDataAssm($paramArray);
        $return_array['role'] = $this->getRoleData($paramArray);

        $sendEmailData = $this->sendEmail($return_array, $paramArray);
        //        $sendEmailData = $this->test_email_send();


        echo json_encode($sendEmailData);
    }

    private function sendEmail($data, $paramArray)
    {
        $emailFrom = 'dms.dist.e@gmail.com';
        $emailAlias = 'dms-dist@no-reply.com';

        $notes = $paramArray['notes'];
        $tipe_approval = $paramArray['tipe_approval'];
        $tanggal = $data['store']['date'];
        $document_number = $data['store']['document_number'];
        $nama_toko = $data['store']['store_name'];
        $kode_toko = $data['store']['store_code'];
        $nama_depo = $data['store']['depo_name'];
        $kode_depo = $data['store']['depo_code'];
        $status_1 = $data['store']['rsm_status'];
        $status_2 = $data['store']['routeplan_status'];
        $status_3 = $data['store']['faro_status'];

        $role = $data['role'];
        $base_url = base_url();

        if ($tipe_approval == 'add') {

            //            $emailTo = implode( '|' , $data[ 'ho' ] );
            //            $emailCc = implode( '|' , $data[ 'ho_cc' ] );
            $emailTo = $data['ho'];
            $emailCc = $data['ho_cc'];
            //            $emailTo = 'lasardi04@gmail.com';
            //            $emailCc = 'ghaliarkan@gmail.com';
            $emailSubject = 'PENGAJUAN NOO ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];


            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">NOO APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. ROUTE PLANNING, RSM, FA RO</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan NOO telah dibuat tanggal <b>$tanggal</b> dengan nomor dokumen pengajuan <b>$document_number</b>.</td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Toko</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Toko</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Entity</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Silahkan ditindaklanjuti dengan membuka link ini. <b><a href='$base_url'>Link</a></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><p>Best Regards,</p><p>ASS/ASM</p></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url . 'asset/images/logoBrowser.png" /></td>';
            $str .= '</tr>';
            $str .= '</table>';



            //            $str .= "<h4>Yth. ROUTE PLANNING, RSM, FA RO</h4><br>";
            //            $str .= "<p>Dokumen pengajuan NOO telah dibuat tanggal $tanggal dengan nomor dokumen pengajuan $document_number.</p>";
            //            $str .= "<p>&nbsp;&nbsp;Nama Toko : $nama_toko</p>";
            //            $str .= "<p>&nbsp;&nbsp;Kode Toko : $kode_toko</p>";
            //            $str .= "<p>&nbsp;&nbsp;Nama Entity : ($kode_depo) $nama_depo</p>";
            //            $str .= "<p>Silahkan ditindaklanjuti dengan membuka link ini. <a href='$base_url'>Link</a> </p><br><br>";
            //            $str .= '<p>Best Regards,</p>';
            //            $str .= '<p>ASS/ASM</p>';

            $emailBody = $str;
        } else {
            if ($tipe_approval == 'nr') {

                //                $emailTo = implode( '|' , $data[ 'assm' ] );
                //                $emailCc = implode( '|' , $data[ 'ho_cc' ] );
                $emailTo = $data['assm'];
                $emailCc = $data['ho_cc'];
                $emailSubject = 'REVISI PENGAJUAN NOO ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];


                $str = '';
                $str .= '<table style="width="100%;border:none;">';
                $str .= '<tr>';
                $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">NOO APPROVAL</td>';
                $str .= '</tr>';
                $str .= '<tr>';
                $str .= '<td><b><h4>Yth. ASS/ASM</h4></b></td>';
                $str .= '</tr>';
                $str .= '<tr>';
                $str .= "<td>Dokumen pengajuan NOO yang dibuat dengan nomor dokumen pengajuan <b>$document_number</b> BUTUH DIREVISI.</td>";
                $str .= '</tr>';
                $str .= '<tr>';
                $str .= '<table>';
                $str .= '<tr>';
                $str .= "<td>Nama Toko</td><td>:<b> $nama_toko </b></td>";
                $str .= '</tr>';
                $str .= '<tr>';
                $str .= "<td>Kode Toko</td><td>:<b> $kode_toko </b> </td>";
                $str .= '</tr>';
                $str .= '<tr>';
                $str .= "<td>Nama Entity</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
                $str .= '</tr>';
                $str .= '<tr>';
                $str .= "<td>Revisi</td><td>:<b> $notes </b></td>";
                $str .= '</tr>';
                $str .= '</table>';
                $str .= '</tr>';
                $str .= '<tr>';
                $str .= "<td>Silahkan ditindaklanjuti dengan membuka link ini. <b><a href='$base_url'>Link</a></b></td>";
                $str .= '</tr>';
                $str .= '<tr>';
                $str .= "<td><b><p>Best Regards,</p><p>$role</p></b></td>";
                $str .= '</tr>';
                $str .= '<tr>';
                $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url . 'asset/images/logoBrowser.png" /></td>';
                $str .= '</tr>';
                $str .= '</table>';

                //                $str = '';
                //                $str .= "<h4>Yth. ASS/ASM</h4><br>";
                //                $str .= "<p>Dokumen pengajuan NOO yang dibuat dengan nomor dokumen pengajuan <b>$document_number</b> BUTUH DIREVISI.</p>";
                //                $str .= "<p>&nbsp;&nbsp;Nama Toko : $nama_toko</p>";
                //                $str .= "<p>&nbsp;&nbsp;Kode Toko : $kode_toko</p>";
                //                $str .= "<p>&nbsp;&nbsp;Nama Entity : ($kode_depo) $nama_depo</p>";
                //                $str .= "<p>&nbsp;&nbsp;Revisi : $notes</p>";
                //                $str .= "<p>Silahkan ditindaklanjuti dengan membuka link ini. <a href='$base_url'>Link</a> </p><br><br>";
                //                $str .= '<p>Best Regards,</p>';
                //                $str .= "<p>$role</p>";

                $emailBody = $str;
            } else {
                if ($tipe_approval == 'fr') {

                    //                    $emailTo = implode( '|' , $data[ 'ho' ] );
                    //                    $emailCc = implode( '|' , $data[ 'ho_cc' ] );
                    $emailTo = $data['ho'];
                    $emailCc = $data['ho_cc'];
                    $emailSubject = 'REVISI PENGAJUAN NOO ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

                    $str = '';
                    $str .= '<table style="width="100%;border:none;">';
                    $str .= '<tr>';
                    $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">NOO APPROVAL</td>';
                    $str .= '</tr>';
                    $str .= '<tr>';
                    $str .= '<td><b><h4>Yth. ROUTE PLANNING, RSM, FA RO</h4></b></td>';
                    $str .= '</tr>';
                    $str .= '<tr>';
                    $str .= "<td>Dokumen pengajuan NOO yang dibuat dengan nomor dokumen pengajuan <b>$document_number</b> SUDAH DIREVISI.</td>";
                    $str .= '</tr>';
                    $str .= '<tr>';
                    $str .= '<table>';
                    $str .= '<tr>';
                    $str .= "<td>Nama Toko</td><td>:<b> $nama_toko </b></td>";
                    $str .= '</tr>';
                    $str .= '<tr>';
                    $str .= "<td>Kode Toko</td><td>:<b> $kode_toko </b> </td>";
                    $str .= '</tr>';
                    $str .= '<tr>';
                    $str .= "<td>Nama Entity</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
                    $str .= '</tr>';
                    $str .= '</table>';
                    $str .= '</tr>';
                    $str .= '<tr>';
                    $str .= "<td>Silahkan ditindaklanjuti dengan membuka link ini. <b><a href='$base_url'>Link</a></b></td>";
                    $str .= '</tr>';
                    $str .= '<tr>';
                    $str .= "<td><b><p>Best Regards,</p><p>ASS/ASM</p></b></td>";
                    $str .= '</tr>';
                    $str .= '<tr>';
                    $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url . 'asset/images/logoBrowser.png" /></td>';
                    $str .= '</tr>';
                    $str .= '</table>';

                    //                    $str = '';
                    //                    $str .= "<h4>Yth. ROUTE PLANNING, RSM, FA RO</h4><br>";
                    //                    $str .= "<p>Dokumen pengajuan NOO yang dibuat dengan nomor dokumen pengajuan <b>$document_number</b> SUDAH DIREVISI.</p>";
                    //                    $str .= "<p>&nbsp;&nbsp;Nama Toko : $nama_toko</p>";
                    //                    $str .= "<p>&nbsp;&nbsp;Kode Toko : $kode_toko</p>";
                    //                    $str .= "<p>&nbsp;&nbsp;Nama Entity : ($kode_depo) $nama_depo</p>";
                    //                    $str .= "<p>Silahkan ditindaklanjuti dengan membuka link ini. <a href='$base_url'>Link</a> </p><br><br>";
                    //                    $str .= '<p>Best Regards,</p>';
                    //                    $str .= '<p>ASS/ASM</p>';

                    $emailBody = $str;
                } else {
                    if ($tipe_approval == 'app') {

                        //                        $emailTo = implode( '|' , $data[ 'assm' ] );
                        //                        $emailCc = implode( '|' , $data[ 'ho_cc' ] );
                        $emailTo = $data['assm'];
                        $emailCc = $data['ho_cc'];
                        $emailSubject = 'PENGAJUAN NOO ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

                        $str = '';
                        $str .= '<table style="width="100%;border:none;">';
                        $str .= '<tr>';
                        $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">NOO APPROVAL</td>';
                        $str .= '</tr>';
                        $str .= '<tr>';
                        $str .= '<td><b><h4>Yth. ASS/ASM</h4></b></td>';
                        $str .= '</tr>';
                        $str .= '<tr>';
                        $str .= "<td>Dokumen pengajuan NOO yang dibuat dengan nomor dokumen pengajuan <b>$document_number</b> BERHASIL DISETUJUI OLEH $role.</td>";
                        $str .= '</tr>';
                        $str .= '<tr>';
                        $str .= '<table>';
                        $str .= '<tr>';
                        $str .= "<td>Nama Toko</td><td>:<b> $nama_toko </b></td>";
                        $str .= '</tr>';
                        $str .= '<tr>';
                        $str .= "<td>Kode Toko</td><td>:<b> $kode_toko </b> </td>";
                        $str .= '</tr>';
                        $str .= '<tr>';
                        $str .= "<td>Nama Entity</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
                        $str .= '</tr>';
                        $str .= '</table>';
                        $str .= '</tr>';
                        $str .= '<tr>';
                        if ($status_1 == 'A' && $status_2 == 'A' && $status_3 == 'A') {
                            $str .= "<td>Toko tersebut sudah aktif dan dapat  bertransaksi.</td>";
                        } else {
                            $str .= "<td>Toko akan aktif jika RSM, Route Planning, FA RO telah APPROVE.</td>";
                        }
                        $str .= '</tr>';
                        $str .= '<tr>';
                        $str .= "<td><b><p>Best Regards,</p><p>$role</p></b></td>";
                        $str .= '</tr>';
                        $str .= '<tr>';
                        $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url . 'asset/images/logoBrowser.png" /></td>';
                        $str .= '</tr>';
                        $str .= '</table>';

                        //                        $str = '';
                        //                        $str .= "<h4>Yth. ASS/ASM</h4><br>";
                        //                        $str .= "<p>Dokumen pengajuan NOO yang dibuat dengan nomor dokumen pengajuan <b>$document_number</b> BERHASIL DISETUJUI OLEH $role.</p>";
                        //                        $str .= "<p>&nbsp;&nbsp;Nama Toko : $nama_toko</p>";
                        //                        $str .= "<p>&nbsp;&nbsp;Kode Toko : $kode_toko</p>";
                        //                        $str .= "<p>&nbsp;&nbsp;Nama Entity : ($kode_depo) $nama_depo</p>";
                        //            $str .= "<p>&nbsp;&nbsp;Notes : $notes</p>";
                        //                        if ( $status_1 == 'A' && $status_2 == 'A' && $status_3 == 'A' ) {
                        //                            $str .= "<p>Toko akan aktif jika RSM, Route Planning, FA RO telah APPROVE.</p><br><br>";
                        //                        } else {
                        //                            $str .= "<p>Toko tersebut sudah aktif dan dapat  bertransaksi.</p><br><br>";
                        //                        }
                        //                        $str .= '<p>Best Regards,</p>';
                        //                        $str .= "<p>$role</p>";

                        $emailBody = $str;
                    }
                }
            }
        }

        if ($emailTo != null) {
            $mailin = new Mailin("https://api.sendinblue.com/v2.0", "2wsSyQEdtV0bWIZ6");
            $data_email['to'] = $emailTo;
            if ($emailCc != null) {
                $data_email['cc'] = $emailCc;
            }
            $data_email['from'] = array("dms-system@pitjarus.co", "DMS SYSTEM");
            $data_email['subject'] = $emailSubject;
            $data_email['html'] = $emailBody;
            $data_email['headers'] = array("Content-Type" => "text/html; charset=iso-8859-1");


            $res = $mailin->send_email($data_email);

            $send = $res["code"] == "success";

            return ($send) ? "SUCCESS" : "FAILED";
        } else {
            return 'FAILED';
        }

        return $status_3;
    }

    private function test_email_send()
    {

        $mailin = new Mailin("https://api.sendinblue.com/v2.0", "2wsSyQEdtV0bWIZ6");
        #"xsmtpsib-20f6c3645e8c83f82640dcb8a32fdf6de9e3192f52aaacf19f9f2affda0f8e1c-EOb3tc6LJAw425vX");
        $data = array(
            "to" => array("abdurrahmanariyanto@gmail.com" => "Riyan"),
            "cc" => array("abdurrahman.ariyanto19@gmail.com" => 'Abdurrahman ariyanto'),
            "from" => array("dms-system@pitjarus.co", "DMS SYSTEM"),
            "subject" => "TEST EMAIL",
            "html" => "This is the <h1>HTML lala</h1>",
            "headers" => array("Content-Type" => "text/html; charset=iso-8859-1")
            #"attachment" => array("https://domain.com/path-to-file/filename1.pdf", "https://domain.com/path-to-file/filename2.jpg")
        );

        var_dump($mailin->send_email($data));
    }

    private function getRoleData($paramArray)
    {
        $this->db->select('wr.nama_role');
        $this->db->from('web_role wr');
        $this->db->where('wr.id_web_role', $paramArray['role']);

        $result = $this->db->get();

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->row('nama_role');

        return $result;
    }

    private function getDataAssm($paramArray)
    {

        $platform = $paramArray['platform'];


        $this->db->select('wu.email as email, wu.fullname as name');
        $this->db->from('web_depo_coverage wdc');
        $this->db->join('web_user wu', 'wu.id_web_user = wdc.id_web_user');
        $this->db->where('wdc.depo_id', $paramArray['depo_id']);
        $this->db->where_in('wu.role_id', array('7', '8'));
        $this->db->where("wu.email is not null", null, false);

        //        if ( $platform == 'web' ) {
        //            $this->db->select( 'wu.email as email, wu.fullname as name' );
        //            $this->db->from( 'web_depo_coverage wdc' );
        //            $this->db->join( 'web_user wu' , 'wu.id_web_user = wdc.id_web_user' );
        //            $this->db->where( 'wdc.depo_id' , $paramArray[ 'depo_id' ] );
        //            $this->db->where_in( 'wu.role_id' , array( '7' , '8' ) );
        //        } else {
        //            $this->db->select( 'ul.email' );
        //            $this->db->from( 'user_login ul' );
        //            $this->db->where( 'ul.depo_id' , $paramArray[ 'depo_id' ] );
        //            $this->db->where( "ul.role_id not in(1,2)" , NULL , FALSE );
        //        }
        $result = $this->db->get();

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->result();

        foreach ($result as $key => $value) {
            $return_arr[$value->email] = $value->name;
        }

        return $return_arr;
    }

    private function getDataHocc($paramArray)
    {
        $return_arr = array();


        $this->db->select('wu.email as email, wu.fullname as name');
        $this->db->from('web_depo_coverage wdc');
        $this->db->join('web_user wu', 'wu.id_web_user = wdc.id_web_user');
        $this->db->where('wdc.depo_id', $paramArray['depo_id']);
        $this->db->where_in('wu.role_id', array('13', '14', '18', '21'));
        $this->db->where("wu.email is not null", null, false);

        $result = $this->db->get();

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->result();

        foreach ($result as $key => $value) {
            $return_arr[$value->email] = $value->name;
        }

        return $return_arr;
    }

    private function getDataHo($paramArray)
    {
        $return_arr = array();


        $this->db->select('wu.email as email, wu.fullname as name');
        $this->db->from('web_depo_coverage wdc');
        $this->db->join('web_user wu', 'wu.id_web_user = wdc.id_web_user');
        $this->db->where('wdc.depo_id', $paramArray['depo_id']);
        $this->db->where_in('wu.role_id', array('12', '19', '20'));

        $result = $this->db->get();
        //        echo $this->db->last_query();die;

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->result();

        $arr_conc = '';

        foreach ($result as $key => $value) {
            $return_arr[$value->email] = $value->name;
        }

        return $return_arr;
    }

    private function getDataStore($paramArray)
    {
        $return_arr = array();

        $this->db->select('DATE(snn.created_date) as tanggal');
        $this->db->select('snn.document_number as document_number');

        $this->db->select('st.store_code as store_code');
        $this->db->select('st.store_name as store_name');
        $this->db->select('dp.depo_code as depo_code');
        $this->db->select('dp.depo_name as depo_name');

        $this->db->select('snn.last_status_by_rsm as rsm_status');
        $this->db->select('snn.last_status_by_routeplan as routplan_status');
        $this->db->select('snn.last_status_by_faro as faro_status');

        $this->db->from('store_noo_new snn');
        $this->db->join('store st', 'st.store_id = snn.store_id');
        $this->db->join('depo dp', 'dp.depo_id = st.depo_id');
        $this->db->where('snn.store_noo_id', $paramArray['noo_id']);

        $result = $this->db->get();
        //        echo $this->db->last_query();die;

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->result();

        foreach ($result as $key => $value) {
            $return_arr['date'] = $value->tanggal;
            $return_arr['document_number'] = $value->document_number;
            $return_arr['store_code'] = $value->store_code;
            $return_arr['store_name'] = $value->store_name;
            $return_arr['depo_code'] = $value->depo_code;
            $return_arr['depo_name'] = $value->depo_name;
            $return_arr['rsm_status'] = $value->rsm_status;
            $return_arr['routeplan_status'] = $value->routplan_status;
            $return_arr['faro_status'] = $value->faro_status;
        }

        return $return_arr;
    }

    /** group store exit */
    public function sentMailStoreExit($paramArray)
    {
        $return_array = array();
        $return_data = array();

        $return_array['store'] = $this->getDataStoreExit($paramArray);
        $return_array['ho'] = $this->getDataHr($paramArray);
        // print_r($return_array['store']);die;
        // $return_array[ 'ho_cc' ] = $this->getDataHocc( $paramArray );
        // $return_array[ 'assm' ] = $this->getDataAssm( $paramArray );
        // $return_array[ 'role' ] = $this->getRoleData( $paramArray );

        $sendEmailData = $this->sendEmailExitStore($return_array, $paramArray);
        // $sendEmailData = $this->test_email_send();


        echo json_encode($sendEmailData);
    }

    private function getDataStoreExit($paramArray)
    {
        $return_arr = array();

        $this->db->select('se.id_exit,
            se.store_id,
            se.created_date as tanggal,
            se.document_number,
            s.store_code,
            s.store_name,
            s.owner_no_ktp,
            d.depo_code,
            d.depo_name,
            ses.status_name as status,
            wu.fullname as created_by,
            wr.nama_role')
            ->from('store_exit se')
            ->join('store s', 's.store_id = se.store_id')
            ->join('depo d', 'd.depo_id = s.depo_id')
            ->join('store_exit_status ses', 'ses.status_code = se.last_status')
            ->join('web_user wu', 'wu.id_web_user = se.created_by')
            ->join('web_role wr', 'wr.id_web_role = wu.role_id')
            ->where('se.store_id', $paramArray['store_id']);

        $result = $this->db->get();
        // echo $this->db->last_query();die;

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->result();

        foreach ($result as $key => $value) {
            $return_arr['date'] = $value->tanggal;
            $return_arr['document_number'] = $value->document_number;
            $return_arr['store_code'] = $value->store_code;
            $return_arr['store_name'] = $value->store_name;
            $return_arr['depo_code'] = $value->depo_code;
            $return_arr['depo_name'] = $value->depo_name;
            $return_arr['status'] = $value->status;
            $return_arr['owner_no_ktp'] = $value->owner_no_ktp;
        }

        return $return_arr;
    }

    private function getDataHr($paramArray)
    {
        $return_arr = array();


        $this->db->select('wu.email as email, wu.fullname as name');
        $this->db->from('web_user wu');
        $this->db->where('wu.email is not null');
        $this->db->where('wu.email !=', '');
        if ($paramArray['tipe_approval'] == 'add') {
            $this->db->join('web_depo_coverage wdc', 'wu.id_web_user = wdc.id_web_user');
            $this->db->where('wdc.depo_id', $paramArray['depo_id']);
            $this->db->where_in('wu.role_id', array(25, 11, 23, 20, 19));
        } else {
            $this->db->where('wu.depo_id', $paramArray['depo_id']);
            $this->db->where_in('wu.role_id', array(3));
        }


        $result = $this->db->get();

        // echo $this->db->last_query();
        // die;

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->result();

        $arr_conc = '';

        foreach ($result as $key => $value) {
            $return_arr[$value->email] = $value->name;
        }

        return $return_arr;
    }

    private function sendEmailExitStore($data, $paramArray)
    {
        $emailFrom = 'dms.dist.e@gmail.com';
        $emailAlias = 'dms-dist@no-reply.com';

        $notes = $paramArray['notes'];
        $tipe_approval = $paramArray['tipe_approval'];
        $tanggal = $data['store']['date'];
        $nama_toko = $data['store']['store_name'];
        $kode_toko = $data['store']['store_code'];
        $nama_depo = $data['store']['depo_name'];
        $kode_depo = $data['store']['depo_code'];
        $status_1 = $data['store']['status'];
        $no_ktp = $data['store']['owner_no_ktp'];
        $document_number = $data['store']['document_number'];

        $base_url = base_url() . 'master/entity/exit_clearence/';
        $base_url_img = base_url() . 'asset/images/logoBrowser.png';

        if ($tipe_approval == 'add') {

            $emailTo = $data['ho'];
            $emailSubject = 'PENGAJUAN Exit Hawker ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">EXIT HAWKER APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. ROUTE PLANNING, RSM, FA RO</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan Exit Hawker telah dibuat tanggal <b>" . date('d/m/Y', strtotime($tanggal)) . "</b> dengan nomor dokumen pengajuan <b>$document_number</b>. </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Hawker</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Hawker</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Entity</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>NIK</td><td>:<b> $no_ktp </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Silahkan ditindaklanjuti dengan membuka link ini. <b><a href='$base_url'>Link</a></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><p>Best Regards,</p><p>ASS/ASM</p></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url_img . '" /></td>';
            $str .= '</tr>';
            $str .= '</table>';


            $emailBody = $str;
        } else if ($tipe_approval == 'app') {
            $emailTo = $data['ho'];
            $emailSubject = 'PENGAJUAN Exit Hawker ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">EXIT HAWKER APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. ROUTE PLANNING, RSM, FA RO</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan Exit Hawker telah dibuat tanggal <b>" . date('d/m/Y', strtotime($tanggal)) . "</b> dengan nomor dokumen pengajuan <b>$document_number</b>. </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Hawker</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Hawker</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Entity</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>NIK</td><td>:<b> $no_ktp </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Silahkan ditindaklanjuti dengan membuka link ini. <b><a href='$base_url'>Link</a></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td><b><p>Best Regards,</p><p>FA RO</p></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url_img . '/></td>';
            $str .= '</tr>';
            $str .= '</table>';

            $emailBody = $str;
        } else if ($tipe_approval == 'nr') {
            $emailTo = $data['ho'];
            $emailSubject = 'REVISI PENGAJUAN EXIT HAWKER ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">EXIT HAWKER APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. ASS / ASM</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan Exit Hawker dengan nomor dokumen pengajuan <b>$document_number <i>Butuh Direvisi</i></b>.</td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Hawker</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Hawker</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Entity</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>NIK</td><td>:<b> $no_ktp </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Silahkan ditindaklanjuti dengan membuka link ini. <b><a href='$base_url'>Link</a></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td><b><p>Best Regards,</p><p>FA RO</p></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url_img . '/></td>';
            $str .= '</tr>';
            $str .= '</table>';

            $emailBody = $str;
        } else if ($tipe_approval == 'fr') {
            $emailTo = $data['ho'];
            $emailSubject = 'REVISI PENGAJUAN EXIT HAWKER ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">EXIT HAWKER APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. FA RO</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan Exit Hawker dengan nomor dokumen pengajuan <b>$document_number <i>Sudah Direvisi</i></b>.</td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Hawker</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Hawker</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Entity</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>NIK</td><td>:<b> $no_ktp </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Silahkan ditindaklanjuti dengan membuka link ini. <b><a href='$base_url'>Link</a></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td><b><p>Best Regards,</p><p>ASS / ASM</p></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url_img . '/></td>';
            $str .= '</tr>';
            $str .= '</table>';

            $emailBody = $str;
        } else if ($tipe_approval == 'A') {
            $emailTo = $data['ho'];
            $emailSubject = 'APPROVE PENGAJUAN EXIT HAWKER ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">EXIT HAWKER APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. FA RO</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan Exit Hawker dengan nomor dokumen pengajuan <b>$document_number</b> sudah di <b><i>Approve</i></b>.</td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Hawker</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Hawker</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Entity</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>NIK</td><td>:<b> $no_ktp </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td><b><p>Best Regards,</p><p>ASS / ASM</p></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url_img . '/></td>';
            $str .= '</tr>';
            $str .= '</table>';

            $emailBody = $str;
        } else if ($tipe_approval == 'R') {
            $emailTo = $data['ho'];
            $emailSubject = 'REJECT PENGAJUAN EXIT HAWKER ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">EXIT HAWKER APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. FA RO</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan Exit Hawker dengan nomor dokumen pengajuan <b>$document_number</b> telah di <b><i>Reject</i></b>.</td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Hawker</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Hawker</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Entity</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>NIK</td><td>:<b> $no_ktp </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td><b><p>Best Regards,</p><p>ASS / ASM</p></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url_img . '/></td>';
            $str .= '</tr>';
            $str .= '</table>';

            $emailBody = $str;
        }

        if (isset($emailTo) && $emailTo != null) {
            $mailin = new Mailin("https://api.sendinblue.com/v2.0", "2wsSyQEdtV0bWIZ6");
            $data_email['to'] = $emailTo;

            $data_email['from'] = array("dms-system@pitjarus.co", "DMS SYSTEM");
            $data_email['subject'] = $emailSubject;
            $data_email['html'] = $emailBody;
            $data_email['headers'] = array("Content-Type" => "text/html; charset=iso-8859-1");


            $res = $mailin->send_email($data_email);
            $send = $res["code"] == "success";

            $email_log = array();
            $email_log['module'] = 'exit_clearance';
            $email_log['action'] = $tipe_approval;
            $email_log['transaction_id'] = $paramArray['store_id'];
            $email_log['mail_to_json'] = json_encode($emailTo);
            $email_log['res_json'] = json_encode($res);
            $email_log['platform'] = 'web';
            $this->db->insert("email_log", $email_log);

            return ($send) ? "SUCCESS" : "FAILED";
        } else {
            return 'FAILED, Mailto not found';
        }

        // echo $this->db->last_query();
        // echo json_encode($res);
        // die;
    }
    /** end group store exit */

    /** group hawker approval */
    public function sentMailHawkerApproval($paramArray)
    {
        $return_array = array();
        $return_data = array();
        // echo json_encode( $paramArray ); die();

        $return_array['store'] = $this->getDataStoreHawker($paramArray);
        $return_array['ho'] = $this->getDataEmail($paramArray);
        // echo json_encode( $return_array ); die();

        $sendEmailData = $this->sendEmailApproval($return_array, $paramArray);


        echo json_encode($sendEmailData);
    }

    private function getDataStoreHawker($paramArray)
    {
        $return_arr = array();

        $this->db->select('sa.store_approval_id,
            sa.store_id,
            sa.created_date,
            sa.document_number,
            s.store_code,
            s.store_name,
            s.owner_no_ktp,
            st.store_type_name,
            d.depo_code,
            d.depo_name,
            p.plant_name,
            sas.status_name as status,
            wu.fullname as created_by,
            wr.nama_role')
            ->from('store_approval sa')
            ->join('store s', 's.store_id = sa.store_id')
            ->join('store_type st', 'st.store_type_id = s.store_type_id')
            ->join('depo d', 'd.depo_id = s.depo_id')
            ->join('plant p', 'p.plant_id = d.plant_id')
            ->join('store_approval_status sas', 'sas.status_code = sa.last_status')
            ->join('web_user wu', 'wu.id_web_user = sa.created_by')
            ->join('web_role wr', 'wr.id_web_role = wu.role_id')
            ->where('sa.store_approval_id', $paramArray['store_approval_id']);

        $result = $this->db->get();
        // echo $this->db->last_query();die;

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->result();

        foreach ($result as $key => $value) {
            $return_arr['date'] = $value->created_date;
            $return_arr['document_number'] = $value->document_number;
            $return_arr['store_code'] = $value->store_code;
            $return_arr['store_name'] = $value->store_name;
            $return_arr['depo_code'] = $value->depo_code;
            $return_arr['depo_name'] = $value->depo_name;
            $return_arr['plant_name'] = $value->plant_name;
            $return_arr['status'] = $value->status;
            $return_arr['owner_no_ktp'] = $value->owner_no_ktp;
            $return_arr['store_type'] = $value->store_type_name;
        }

        return $return_arr;
    }

    private function getDataEmail($paramArray)
    {
        $return_arr = array();

        /** cek user coverage */
        $id_user = is_null($this->session->userdata('id_user')) ? 1275 : $this->session->userdata('id_user');
        $get_user = $this->db->get_where('web_user', array('id_web_user' => $id_user))->row();
        if ($get_user->depo_id == 0) {
            $this->db->join('web_depo_coverage wdc', 'wu.id_web_user = wdc.id_web_user');
            $this->db->where('wdc.depo_id', $paramArray['depo_id']);
        } else {
            $this->db->where('wu.depo_id', $paramArray['depo_id']);
        }

        $this->db->select('wu.email as email, wu.fullname as name');
        $this->db->from('web_user wu');
        $this->db->where('wu.email is not null');

        $arr_ass = array('N', 'FR');
        if (in_array($paramArray['status_type'], $arr_ass)) {
            /**role atasan */
            $this->db->where_in('wu.role_id', array(25, 12));
        } else {
            /**role bawahan */
            $this->db->where_in('wu.role_id', array(7, 8));
        }
        $this->db->group_by('wu.email');


        $result = $this->db->get();

        // echo $this->db->last_query();die;

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->result();

        foreach ($result as $key => $value) {
            $return_arr[$value->email] = $value->name;
        }

        return $return_arr;
    }

    private function sendEmailApproval($data, $paramArray)
    {
        $emailFrom = 'dms.dist.e@gmail.com';
        $emailAlias = 'dms-dist@no-reply.com';

        $notes = $paramArray['notes'];
        $tipe_approval = trim($paramArray['status_type']);
        $tanggal = $data['store']['date'];
        $nama_toko = $data['store']['store_name'];
        $kode_toko = $data['store']['store_code'];
        $nama_depo = $data['store']['depo_name'];
        $kode_depo = $data['store']['depo_code'];
        $nama_plant = $data['store']['plant_name'];
        $status_1 = $data['store']['status'];
        $no_ktp = $data['store']['owner_no_ktp'];
        $document_number = $data['store']['document_number'];
        $store_type = $data['store']['store_type'];

        $base_url = base_url() . 'tools/hawker_approval/';
        $base_url_img = base_url() . 'asset/images/logoBrowser.png';

        if ($tipe_approval == 'N') {

            $emailTo = $data['ho'];
            $emailSubject = 'PENGAJUAN HAWKER BARU ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">HAWKER APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. HRD</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan Hawker Baru telah dibuat tanggal <b>" . date('d F Y', strtotime($tanggal)) . "</b> dengan nomor dokumen pengajuan <b>$document_number</b>. </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Plant</td><td>:<b> $nama_plant </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Depo</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Toko/Hawker</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Toko/Hawker</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Role/Tipe</td><td>:<b> $store_type </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Silahkan ditindaklanjuti dengan membuka link ini. <b><a href='$base_url'>LINK</a></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><p>Best Regards,</p><p>ASS/ASM</p></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url_img . '" style="height: 80px;"/></td>';
            $str .= '</tr>';
            $str .= '</table>';


            $emailBody = $str;
        } else if ($tipe_approval == 'NR') {
            $emailTo = $data['ho'];
            $emailSubject = 'REVISI PENGAJUAN HAWKER BARU ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">HAWKER APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. ASS / ASM</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan Hawker Baru dengan nomor dokumen pengajuan <b>$document_number <i>Butuh Direvisi</i></b>.</td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Plant</td><td>:<b> $nama_plant </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Depo</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Toko/Hawker</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Toko/Hawker</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Role/Tipe</td><td>:<b> $store_type </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Silahkan ditindaklanjuti dengan membuka link ini. <b><a href='$base_url'>LINK</a></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td><b><p>Best Regards,</p><p>HRD</p></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url_img . '" style="height: 80px;"/></td>';
            $str .= '</tr>';
            $str .= '</table>';

            $emailBody = $str;
        } else if ($tipe_approval == 'FR') {
            $emailTo = $data['ho'];
            $emailSubject = 'REVISI PENGAJUAN HAWKER BARU ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">HAWKER APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. HRD</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan Hawker Baru dengan nomor dokumen pengajuan <b>$document_number <i>Telah Direvisi</i></b>.</td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Plant</td><td>:<b> $nama_plant </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Depo</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Toko/Hawker</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Toko/Hawker</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Role/Tipe</td><td>:<b> $store_type </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Silahkan ditindaklanjuti dengan membuka link ini. <b><a href='$base_url'>LINK</a></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td><b><p>Best Regards,</p><p>ASS / ASM</p></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url_img . '" style="height: 80px;"/></td>';
            $str .= '</tr>';
            $str .= '</table>';

            $emailBody = $str;
        } else if ($tipe_approval == 'A') {
            $emailTo = $data['ho'];
            $emailSubject = 'APPROVE PENGAJUAN HAWKER BARU ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">HAWKER APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. ASS / ASM</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan Hawker Baru dengan nomor dokumen pengajuan <b>$document_number</b> sudah di <b><i>Approve</i></b>.</td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Plant</td><td>:<b> $nama_plant </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Depo</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Toko/Hawker</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Toko/Hawker</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Role/Tipe</td><td>:<b> $store_type </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td><b><p>Best Regards,</p><p>HRD</p></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url_img . '" style="height: 80px;"/></td>';
            $str .= '</tr>';
            $str .= '</table>';

            $emailBody = $str;
        } else if ($tipe_approval == 'R') {
            $emailTo = $data['ho'];
            $emailSubject = 'REJECT PENGAJUAN HAWKER BARU ' . '(' . $data['store']['depo_code'] . ') ' . $data['store']['depo_name'];

            $str = '';
            $str .= '<table style="width="100%;border:none;">';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">HAWKER APPROVAL</td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td><b><h4>Yth. ASS / ASM</h4></b></td>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Dokumen pengajuan Hawker Baru dengan nomor dokumen pengajuan <b>$document_number</b> telah di <b><i>Reject</i></b>.</td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<table>';
            $str .= '<tr>';
            $str .= "<td>Nama Plant</td><td>:<b> $nama_plant </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Depo</td><td>:<b> ($kode_depo) $nama_depo </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Kode Toko/Hawker</td><td>:<b> $kode_toko </b> </td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Nama Toko/Hawker</td><td>:<b> $nama_toko </b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td>Role/Tipe</td><td>:<b> $store_type </b></td>";
            $str .= '</tr>';
            $str .= '</table>';
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= "<td><b><p>Best Regards,</p><p>HRD</p></b></td>";
            $str .= '</tr>';
            $str .= '<tr>';
            $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img src="' . $base_url_img . '" style="height: 80px;"/></td>';
            $str .= '</tr>';
            $str .= '</table>';

            $emailBody = $str;
        }

        if (isset($emailTo) && $emailTo != null) {
            $mailin = new Mailin("https://api.sendinblue.com/v2.0", "2wsSyQEdtV0bWIZ6");
            $data_email['to'] = $emailTo;

            $data_email['from'] = array("dms-system@pitjarus.co", "DMS SYSTEM");
            $data_email['subject'] = $emailSubject;
            $data_email['html'] = $emailBody;
            $data_email['headers'] = array("Content-Type" => "text/html; charset=iso-8859-1");


            $res = $mailin->send_email($data_email);

            $send = $res["code"] == "success";

            return ($send) ? "SUCCESS" : "FAILED";
        } else {
            return 'FAILED';
        }

        // return $status_3;
    }
    /** end group hawker approval */

    /** group email mutasi depo */
    public function sentMailMutasiDepo($paramArray)
    {
        $mutasiId = $paramArray['mutasiId'];
        $type = $paramArray['type'];

        $dataFrom = $this->getDataFrom($mutasiId);
        $dataTo = $this->getDataTo($mutasiId);

        $sendEmail = $this->sendEmailMutasiDepo(
            $type,
            $dataFrom->mutasi_number,
            $dataFrom->plant_name_from,
            $dataFrom->depo_id_from,
            $dataFrom->depo_code_from,
            $dataFrom->depo_name_from,
            $dataFrom->hawker_id_from,
            $dataFrom->hawker_name_from,
            $dataFrom->hawker_role_from,
            $dataFrom->qty_sent,
            $dataTo->plant_name_to,
            $dataTo->depo_id_to,
            $dataTo->depo_code_to,
            $dataTo->depo_name_to,
            $dataTo->hawker_id_to,
            $dataTo->hawker_name_to,
            $dataTo->hawker_role_to,
            $dataTo->qty_taken
        );
        echo json_encode(array(
            'status' => 'success',
            'mail' => $sendEmail,
            'message' => ''
        ));
    }

    private function getDataFrom($mutasiId)
    {
        $db2 = $this->load->database('default', TRUE);
        $db2->select('m.mutasi_number, p.plant_name as plant_name_from, d.depo_id as depo_id_from, d.depo_code as depo_code_from, d.depo_name as depo_name_from, 
		m.salesman_id_from as hawker_id_from, u.name as hawker_name_from, ur.role_name as hawker_role_from, SUM(qty) as qty_sent')
            ->from('mutasi_depo m')
            ->join('mutasi_depo_detail md', 'md.mutasi_depo_id = m.mutasi_depo_id')
            ->join('user_login u', 'u.user_id = m.salesman_id_from')
            ->join('user_role ur', 'ur.role_id = u.role_id')
            ->join('depo d', 'd.depo_id = m.depo_id_from')
            ->join('plant p', 'p.plant_id = d.plant_id')
            ->where('m.mutasi_depo_id', $mutasiId);
        $result = $db2->get();
        // echo $db2->last_query();die;

        if ($result->num_rows() < 1) {
            return NULL;
        }

        return $result->row();
    }

    private function getDataTo($mutasiId)
    {
        $db2 = $this->load->database('default', TRUE);
        $db2->select('p.plant_name as plant_name_to, d.depo_id as depo_id_to, d.depo_code as depo_code_to, d.depo_name as depo_name_to, 
		m.salesman_id_to as hawker_id_to, u.name as hawker_name_to, ur.role_name as hawker_role_to, SUM(qty_taken) as qty_taken')
            ->from('mutasi_depo m')
            ->join('mutasi_depo_detail md', 'md.mutasi_depo_id = m.mutasi_depo_id')
            ->join('user_login u', 'u.user_id = m.salesman_id_to')
            ->join('user_role ur', 'ur.role_id = u.role_id')
            ->join('depo d', 'd.depo_id = m.depo_id_to')
            ->join('plant p', 'p.plant_id = d.plant_id')
            ->where('m.mutasi_depo_id', $mutasiId);
        $result = $db2->get();
        // echo $db2->last_query();die;

        if ($result->num_rows() < 1) {
            return NULL;
        }

        return $result->row();
    }

    private function sendEmailMutasiDepo($type, $documentNumber, $plantFrom, $depoIdFrom, $depoCodeFrom, $depoNameFrom, $hawkerIdFrom, $hawkerNameFrom, $hawkerRoleFrom, $qtySent, $plantTo, $depoIdTo, $depoCodeTo, $depoNameTo, $hawkerIdTo, $hawkerNameTo, $hawkerRoleTo, $qtyTaken)
    {

        $base_url_img = base_url() . "asset/images/logoBrowser.png";

        /*Generate Body*/
        $link = base_url('tools/mutasi_depo');
        $tanggal = date('d M Y', time());
        $emailArr = array();
        if ($type == 'open') {
            $emailArr['email'] = $this->getEmails($depoIdTo);
        } else {
            $emailArr['email'] = $this->getEmails($depoIdFrom);
        }
        $emailTo = $emailArr['email'];

        $emailSubject = 'MUTASI DEPO' . '(' . $documentNumber . ')';

        $str = '';
        $str .= '<table style="width="100%;border:none;">';
        $str .= '<tr>';
        $str .= '<td style="background-color:#20335C;text-align:center;color:white;font-size:25px;">MUTASI DEPO</td>';
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= '<td><b><h4>Yth. HEADLOG, FINANCE</h4></b></td>';
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>Dokumen pengajuan Mutasi antar depo telah dibuat tanggal <b>$tanggal</b> dengan nomor dokumen pengajuan <b>$documentNumber</b>. </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= '<table>';
        $str .= '<tr>';
        $str .= "<td><b>Pengirim</b></td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>&emsp;&emsp;Nama Plant</td><td>: $plantFrom </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>&emsp;&emsp;Nama Depo</td><td>: ($depoCodeFrom) $depoNameFrom </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>&emsp;&emsp;ID Hawker</td><td>: $hawkerIdFrom </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>&emsp;&emsp;Nama Hawker</td><td>: $hawkerNameFrom </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>&emsp;&emsp;Role</td><td>: $hawkerRoleFrom </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>&emsp;&emsp;QTY</td><td>: $qtySent PCS</td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td> </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td><b>Penerima</b></td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>&emsp;&emsp;Nama Plant</td><td>: $plantTo </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>&emsp;&emsp;Nama Depo</td><td>: ($depoCodeTo) $depoNameTo </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>&emsp;&emsp;ID Hawker</td><td>: $hawkerIdTo </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>&emsp;&emsp;Nama Hawker</td><td>: $hawkerNameTo </td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= "<td>&emsp;&emsp;Role</td><td>: $hawkerRoleTo </td>";
        $str .= '</tr>';
        if ($type == 'taken') {
            $str .= '<tr>';
            $str .= "<td><b>&emsp;&emsp;QTY</b></td><td>:<b> $qtyTaken PCS</b></td>";
            $str .= '</tr>';
        }
        $str .= '</table>';
        $str .= '</tr>';
        if ($type == 'open') {
            $str .= '<tr>';
            $str .= "<td>Silahkan tindaklanjuti karena status <b>BELUM DITERIMA</b></td>";
            $str .= '</tr>';
        }
        if ($type == 'taken') {
            $str .= '<tr>';
            $str .= "<td>Terkait Mutasi antar Depo sudah berstatus <b>DITERIMA</b></td>";
            $str .= '</tr>';
        }
        if ($type == 'cancel') {
            $str .= '<tr>';
            $str .= "<td>Terkait Mutasi antar Depo terkena <b>AUTO CANCEL</b> dikarenakan melewati schedule auto cancel mutasi by system</td>";
            $str .= '</tr>';
        }
        $str .= '<tr>';
        $str .= "<td><b><p>Best Regards,</p><p>SCM</p></b></td>";
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= '<td style="background-color:#20335C;text-align:center;padding-top:10px;"><img style="height: 80px" src="' . $base_url_img . '"/></td>';
        $str .= '</tr>';
        $str .= '</table>';

        $emailBody = $str;

        if ($emailTo != null) {
            $mailin = new Mailin("https://api.sendinblue.com/v2.0", "2wsSyQEdtV0bWIZ6", 60000);
            $data_email['to'] = $emailTo;

            $data_email['from'] = array("dms-system@pitjarus.co", "DMS SYSTEM");
            $data_email['subject'] = $emailSubject;
            $data_email['html'] = $emailBody;
            $data_email['headers'] = array("Content-Type" => "text/html; charset=iso-8859-1");

            $res = $mailin->send_email($data_email);

            $send = $res["code"] == "success";

            return ($send) ? "SUCCESS" : "FAILED SEND EMAIL";
        } else {
            return 'FAILED, Email to not found';
        }
    }

    private function getEmails($depoId)
    {

        $db2 = $this->load->database('default', TRUE);
        $return_arr = array();

        $db2->select('wu.email as email, wu.fullname as name');
        $db2->from('web_user wu');
        $db2->join('web_depo_coverage wdc', 'wdc.id_web_user = wu.id_web_user');
        $db2->where('wdc.depo_id', $depoId);
        $db2->where_in('wu.role_id', array('2', '11'));
        $db2->where('email is not null');
        $result = $db2->get();
        // echo $db2->last_query(); die;

        if ($result->num_rows() < 1) {
            return NULL;
        }

        $result = $result->result();

        foreach ($result as $key => $value) {
            $return_arr[$value->email] = $value->name;
        }

        return $return_arr;
    }
    /** end group email mutasi depo */
}
