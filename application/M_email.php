<?php

class M_email extends CI_Model {

	public function sentMail( $paramArray ) {
		$return_array = array();
		$return_data = array();

		$return_array[ 'store' ] = $this->getDataStore( $paramArray );
		$return_array[ 'ho' ] = $this->getDataHo( $paramArray );
		$return_array[ 'ho_cc' ] = $this->getDataHocc( $paramArray );
		$return_array[ 'assm' ] = $this->getDataAssm( $paramArray );
		$return_array[ 'role' ] = $this->getRoleData( $paramArray );

		$sendEmailData = $this->sendEmail( $return_array , $paramArray );


		echo json_encode( $sendEmailData );
	}

	private function sendEmail( $data , $paramArray ) {
		$emailFrom = 'dms.dist.e@gmail.com';
		$emailAlias = 'dms-dist@no-reply.com';

		$notes = $paramArray[ 'notes' ];
		$tipe_approval = $paramArray[ 'tipe_approval' ];
		$tanggal = $data[ 'store' ][ 'date' ];
		$document_number = $data[ 'store' ][ 'document_number' ];
		$nama_toko = $data[ 'store' ][ 'store_name' ];
		$kode_toko = $data[ 'store' ][ 'store_code' ];
		$nama_depo = $data[ 'store' ][ 'depo_name' ];
		$kode_depo = $data[ 'store' ][ 'depo_code' ];
		$status_1 = $data[ 'store' ][ 'rsm_status' ];
		$status_2 = $data[ 'store' ][ 'routeplan_status' ];
		$status_3 = $data[ 'store' ][ 'faro_status' ];

		$role = $data[ 'role' ];
		$base_url = base_url();

		if ( $tipe_approval == 'add' ) {

			$emailTo = implode( ',' , $data[ 'ho' ] );
			$emailCc = implode( ',' , $data[ 'ho_cc' ] );
			$emailSubject = 'PENGAJUAN NOO ' . '(' . $data[ 'store' ][ 'depo_code' ] . ') ' . $data[ 'store' ][ 'depo_name' ];

			$str = '';
			$str .= "<h4>Yth. ROUTE PLANNING, RSM, FA RO</h4><br>";
			$str .= "<p>Dokumen pengajuan NOO telah dibuat tanggal $tanggal dengan nomor dokumen pengajuan $document_number.</p>";
			$str .= "<p>&nbsp;&nbsp;Nama Toko : $nama_toko</p>";
			$str .= "<p>&nbsp;&nbsp;Kode Toko : $kode_toko</p>";
			$str .= "<p>&nbsp;&nbsp;Nama Entity : ($kode_depo) $nama_depo</p>";
			$str .= "<p>Silahkan ditindaklanjuti dengan membuka link ini. <a href='$base_url'>Link</a> </p><br><br>";
			$str .= '<p>Best Regards,</p>';
			$str .= '<p>ASS/ASM</p>';

			$emailBody = $str;


		} else {
			if ( $tipe_approval == 'nr' ) {

				$emailTo = implode( ',' , $data[ 'assm' ] );
				$emailCc = implode( ',' , $data[ 'ho_cc' ] );
				$emailSubject = 'REVISI PENGAJUAN NOO ' . '(' . $data[ 'store' ][ 'depo_code' ] . ') ' . $data[ 'store' ][ 'depo_name' ];

				$str = '';
				$str .= "<h4>Yth. ASS/ASM</h4><br>";
				$str .= "<p>Dokumen pengajuan NOO yang dibuat dengan nomor dokumen pengajuan <b>$document_number</b> BUTUH DIREVISI.</p>";
				$str .= "<p>&nbsp;&nbsp;Nama Toko : $nama_toko</p>";
				$str .= "<p>&nbsp;&nbsp;Kode Toko : $kode_toko</p>";
				$str .= "<p>&nbsp;&nbsp;Nama Entity : ($kode_depo) $nama_depo</p>";
				$str .= "<p>&nbsp;&nbsp;Revisi : $notes</p>";
				$str .= "<p>Silahkan ditindaklanjuti dengan membuka link ini. <a href='$base_url'>Link</a> </p><br><br>";
				$str .= '<p>Best Regards,</p>';
				$str .= "<p>$role</p>";

				$emailBody = $str;

			} else {
				if ( $tipe_approval == 'fr' ) {

					$emailTo = implode( ',' , $data[ 'ho' ] );
					$emailCc = implode( ',' , $data[ 'ho_cc' ] );
					$emailSubject = 'REVISI PENGAJUAN NOO ' . '(' . $data[ 'store' ][ 'depo_code' ] . ') ' . $data[ 'store' ][ 'depo_name' ];

					$str = '';
					$str .= "<h4>Yth. ROUTE PLANNING, RSM, FA RO</h4><br>";
					$str .= "<p>Dokumen pengajuan NOO yang dibuat dengan nomor dokumen pengajuan <b>$document_number</b> SUDAH DIREVISI.</p>";
					$str .= "<p>&nbsp;&nbsp;Nama Toko : $nama_toko</p>";
					$str .= "<p>&nbsp;&nbsp;Kode Toko : $kode_toko</p>";
					$str .= "<p>&nbsp;&nbsp;Nama Entity : ($kode_depo) $nama_depo</p>";
					$str .= "<p>Silahkan ditindaklanjuti dengan membuka link ini. <a href='$base_url'>Link</a> </p><br><br>";
					$str .= '<p>Best Regards,</p>';
					$str .= '<p>ASS/ASM</p>';

					$emailBody = $str;

				} else {
					if ( $tipe_approval == 'app' ) {

						$emailTo = implode( ',' , $data[ 'assm' ] );
						$emailCc = implode( ',' , $data[ 'ho_cc' ] );
						$emailSubject = 'REVISI PENGAJUAN NOO ' . '(' . $data[ 'store' ][ 'depo_code' ] . ') ' . $data[ 'store' ][ 'depo_name' ];

						$str = '';
						$str .= "<h4>Yth. ASS/ASM</h4><br>";
						$str .= "<p>Dokumen pengajuan NOO yang dibuat dengan nomor dokumen pengajuan <b>$document_number</b> BERHASIL DISETUJUI OLEH $role.</p>";
						$str .= "<p>&nbsp;&nbsp;Nama Toko : $nama_toko</p>";
						$str .= "<p>&nbsp;&nbsp;Kode Toko : $kode_toko</p>";
						$str .= "<p>&nbsp;&nbsp;Nama Entity : ($kode_depo) $nama_depo</p>";
						//            $str .= "<p>&nbsp;&nbsp;Notes : $notes</p>";
						if ( $status_1 == 'A' && $status_2 == 'A' && $status_3 == 'A' ) {
							$str .= "<p>Toko akan aktif jika RSM, Route Planning, FA RO telah APPROVE.</p><br><br>";
						} else {
							$str .= "<p>Toko tersebut sudah aktif dan dapat  bertransaksi.</p><br><br>";
						}
						$str .= '<p>Best Regards,</p>';
						$str .= "<p>$role</p>";

						$emailBody = $str;
					}
				}
			}
		}

		$this->load->library( 'email' );

		$this->email->from( $emailFrom , $emailAlias );
		$this->email->to( $emailTo );
		$this->email->cc( $emailCc );
		$this->email->subject( $emailSubject );
		$this->email->message( $emailBody );

		//Send mail
		if ( $this->email->send() ) {
			return TRUE;
		} else {
			return FALSE;
		}

	}

	private function getRoleData( $paramArray ) {
		$this->db->select( 'wr.nama_role' );
		$this->db->from( 'web_role wr' );
		$this->db->where( 'wr.id_web_role' , $paramArray[ 'role' ] );

		$result = $this->db->get();

		if ( $result->num_rows() < 1 ) {
			return NULL;
		}

		$result = $result->row( 'nama_role' );

		return $result;
	}

	private function getDataAssm( $paramArray ) {

		$platform = $paramArray[ 'platform' ];

		if ( $platform == 'web' ) {
			$this->db->select( 'wu.email' );
			$this->db->from( 'web_depo_coverage wdc' );
			$this->db->join( 'web_user wu' , 'wu.id_web_user = wdc.id_web_user' );
			$this->db->where( 'wdc.depo_id' , $paramArray[ 'depo_id' ] );
			$this->db->where_in( 'wu.role_id' , array( '7' , '8' ) );
		} else {
			$this->db->select( 'ul.email' );
			$this->db->from( 'user_login ul' );
			$this->db->where( 'ul.depo_id' , $paramArray[ 'depo_id' ] );
			$this->db->where( "ul.role_id not in(1,2)" , NULL , FALSE );
		}
		$result = $this->db->get();

		if ( $result->num_rows() < 1 ) {
			return NULL;
		}

		$result = $result->result();

		foreach ( $result as $key => $value ) {
			$return_arr[] = $value->email;
		}

		return $return_arr;
	}

	private function getDataHocc( $paramArray ) {
		$return_arr = array();


		$this->db->select( 'wu.email' );
		$this->db->from( 'web_depo_coverage wdc' );
		$this->db->join( 'web_user wu' , 'wu.id_web_user = wdc.id_web_user' );
		$this->db->where( 'wdc.depo_id' , $paramArray[ 'depo_id' ] );
		$this->db->where_in( 'wu.role_id' , array( '13' , '14' , '18' , '21' ) );

		$result = $this->db->get();

		if ( $result->num_rows() < 1 ) {
			return NULL;
		}

		$result = $result->result();

		foreach ( $result as $key => $value ) {
			$return_arr[] = $value->email;
		}

		return $return_arr;
	}

	private function getDataHo( $paramArray ) {
		$return_arr = array();


		$this->db->select( 'wu.email' );
		$this->db->from( 'web_depo_coverage wdc' );
		$this->db->join( 'web_user wu' , 'wu.id_web_user = wdc.id_web_user' );
		$this->db->where( 'wdc.depo_id' , $paramArray[ 'depo_id' ] );
		$this->db->where_in( 'wu.role_id' , array( '12' , '19' , '20' ) );

		$result = $this->db->get();

		if ( $result->num_rows() < 1 ) {
			return NULL;
		}

		$result = $result->result();

		foreach ( $result as $key => $value ) {
			$return_arr[] = $value->email;
		}

		return $return_arr;
	}

	private function getDataStore( $paramArray ) {
		$return_arr = array();

		$this->db->select( 'DATE(snn.created_date) as tanggal' );
		$this->db->select( 'snn.document_number as document_number' );

		$this->db->select( 'st.store_code as store_code' );
		$this->db->select( 'st.store_name as store_name' );
		$this->db->select( 'dp.depo_code as depo_code' );
		$this->db->select( 'dp.depo_name as depo_name' );

		$this->db->select( 'snn.last_status_by_rsm as rsm_status' );
		$this->db->select( 'snn.last_status_by_routeplan as routplan_status' );
		$this->db->select( 'snn.last_status_by_faro as faro_status' );

		$this->db->from( 'store_noo_new snn' );
		$this->db->join( 'store st' , 'st.store_id = snn.store_id' );
		$this->db->join( 'depo dp' , 'dp.depo_id = st.depo_id' );
		$this->db->where( 'snn.store_noo_id' , $paramArray[ 'noo_id' ] );

		$result = $this->db->get();

		if ( $result->num_rows() < 1 ) {
			return NULL;
		}

		$result = $result->result();

		foreach ( $result as $key => $value ) {
			$return_arr[ 'date' ] = $value->tanggal;
			$return_arr[ 'document_number' ] = $value->document_number;
			$return_arr[ 'store_code' ] = $value->store_code;
			$return_arr[ 'store_name' ] = $value->store_name;
			$return_arr[ 'depo_code' ] = $value->depo_code;
			$return_arr[ 'depo_name' ] = $value->depo_name;
			$return_arr[ 'rsm_status' ] = $value->rsm_status;
			$return_arr[ 'routeplan_status' ] = $value->routplan_status;
			$return_arr[ 'faro_status' ] = $value->faro_status;
		}

		return $return_arr;

	}
}

?>
