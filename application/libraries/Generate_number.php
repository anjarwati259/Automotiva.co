<?php

class Generate_number {

	function get_number_reconcile( $depo_code ) {

		$date = new DateTime();
		$new_date = $date->format('ymdHisu');//format tahun-bulan-tanggal-jam-menit-detik-milliseconds

		if(strlen($depo_code) > 8) $depo_code = substr($depo_code,0,8);
		$fix_number = '84' . $depo_code . $new_date ;
		if(strlen($fix_number) > 24) $fix_number = substr($fix_number,0,24);
		return $fix_number;

	}

	/** update jan 2022, after case double number */
	function get_number_v3( $kode , $depo_code, $salesman_str ) {
		$date = new DateTime();
		$new_date = $date->format('ymdHisu');//format tahun-bulan-tanggal-jam-menit-detik-milliseconds
		$date_str = substr($new_date, 0, -4);
		$dist = substr($depo_code,0,1);
		$depo = substr($depo_code,-4);
		
		$salesman_id = substr($salesman_str,-3);
		if(strlen($salesman_str) == 1){
			$salesman_code = '00'.$salesman_id;
		}else if(strlen($salesman_id) == 2){
			$salesman_code = '0'.$salesman_id;
		}else{
			$salesman_code = $salesman_id;
		}

		$fix_number = $kode . $dist . $depo . $salesman_code . $date_str;
		return $fix_number;
	}

	function get_number( $kode , $depo_code ) {
		$date = date( "ymdHi" , strtotime( "NOW" ) );
		$number = rand( 0 , 9999 );
		$fix_number = $kode . $depo_code . $date . str_pad( $number , 4 , "0" , STR_PAD_LEFT );
		return $fix_number;
	}

	function get_number_v2( $kode , $depo_code , $salesman_id ){
		$date = new DateTime();
		$new_date = $date->format('ymdHisu');//format tahun-bulan-tanggal-jam-menit-detik-milliseconds
		$depo_str = substr_replace($depo_code,'',1,3);
		$salesman_str = substr($salesman_id, -3);

		$date_str = substr($new_date, 0, -2);

		$str_salesman = '';

		if(strlen($salesman_str) == 1){
			$str_salesman = '00'.$salesman_str;
		}else if(strlen($salesman_id) == 2){
			$str_salesman = '0'.$salesman_str;
		}else{
			$str_salesman = $salesman_str;
		}

		$fix_number = $kode . $depo_str . $str_salesman . $date_str;
		return $fix_number;

	}

	function get_number_fp( $kode , $depo_code ) {
		$date = date( "ymdHis" , strtotime( "NOW" ) );
		$number = rand( 0 , 9999999 );
		$fix_depo_code = $depo_code;
		if (strlen($depo_code) > 4 ) {
			$rm = substr( $depo_code , 0 , 1 );
			$rm2 = substr( $depo_code , 4 );
			$fix_depo_code = $rm . $rm2;
		}

		$fix_number = $kode . $fix_depo_code . $date . str_pad( $number , 7 , "0" , STR_PAD_LEFT );
		return $fix_number;
	}

	function get_number_retur_bs_plant( $kode , $depo_code , $row ) {
		$date = date( "ymd" , strtotime( "NOW" ) );
		$str_depo_code = substr( $depo_code , -4 );
		//        $number_row = $row;
		$fix_number = $kode . $str_depo_code . $date . str_pad( $row , 3 , "0" , STR_PAD_LEFT );
		return $fix_number;
	}

    function generateReportNumber($depoCode, $reportType = '55'){
        // $reportType = "55"; 55=report selling
        $d = new DateTime();
        $datetimenow = $d->format("ymdHisv");
        $fix_number = $reportType.$depoCode.substr($datetimenow,0,14);
        return $fix_number;
    }

}

?>
