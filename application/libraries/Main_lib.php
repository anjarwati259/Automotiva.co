<?php

class Main_lib {

	function mlib_getBulan( $param ) {
		$x = intval( $param );
		$namaBulan = array(
			'1' => 'Januari' ,
			'2' => 'Februari' ,
			'3' => 'Maret' ,
			'4' => 'April' ,
			'5' => 'Mei' ,
			'6' => 'Juni' ,
			'7' => 'Juli' ,
			'8' => 'Agustus' ,
			'9' => 'September' ,
			'10' => 'Oktober' ,
			'11' => 'November' ,
			'12' => 'Desember'
		);
		$ret = $namaBulan[ $x ];

		return $ret;
	}

	function mlib_getHari( $param ) {
		$x = intval( $param );
		$namaHari = array(
			'1' => 'Senin' ,
			'2' => 'Selasa' ,
			'3' => 'Rabu' ,
			'4' => 'Kamis' ,
			'5' => 'Jum\'at' ,
			'6' => 'Sabtu' ,
			'7' => 'Minggu'
		);
		$ret = $namaHari[ $x ];

		return $ret;
	}

	function mlib_getRomanNum( $param ) {
		$number = intval( $param );
		$map = array(
			'M' => 1000 ,
			'CM' => 900 ,
			'D' => 500 ,
			'CD' => 400 ,
			'C' => 100 ,
			'XC' => 90 ,
			'L' => 50 ,
			'XL' => 40 ,
			'X' => 10 ,
			'IX' => 9 ,
			'V' => 5 ,
			'IV' => 4 ,
			'I' => 1
		);
		$returnValue = '';
		while ( $number > 0 ) {
			foreach ( $map as $roman => $int ) {
				if ( $number >= $int ) {
					$number -= $int;
					$returnValue .= $roman;
					break;
				}
			}
		}
		return $returnValue;
	}

	function mlib_getDiscountLimit( $param ) {
		$role = $param[ 'role_id' ];
		$pg = $param[ 'product_group_id' ];

		$map = array();
		//sariroti
		$map[ '1' ][ '1' ] = 100;       //Super Admin
		$map[ '1' ][ '2' ] = 38;        //Head Office
		$map[ '1' ][ '3' ] = 10;        //Kepala Depo (Coord)
		$map[ '1' ][ '4' ] = 0;        //Gudang
		$map[ '1' ][ '5' ] = 0;         //Admin Kasir
		$map[ '1' ][ '6' ] = 0;         //Head Office 2
		$map[ '1' ][ '7' ] = 10;        //ASS
		$map[ '1' ][ '8' ] = 10;        //ASM
		$map[ '1' ][ '9' ] = 0;         //Report
		$map[ '1' ][ '10' ] = 0;        //ASSPD
		$map[ '1' ][ '11' ] = 100;      //Head Office Cover
		$map[ '1' ][ '12' ] = 12;       //RSM
		$map[ '1' ][ '13' ] = 15;       //RBUM
		$map[ '1' ][ '14' ] = 38;       //Vice President
		$map[ '1' ][ '15' ] = 0;        //Admin Plant
		$map[ '1' ][ '16' ] = 0;        //HO Cross Cover
		$map[ '1' ][ '17' ] = 0;        //HO Tax
		$map[ '1' ][ '18' ] = 0;        //SCM
		$map[ '1' ][ '19' ] = 0;      //Route Planning
		$map[ '1' ][ '20' ] = 0;      //FA RO
		$map[ '1' ][ '21' ] = 0;      //FA HO
		$map[ '1' ][ '22' ] = 10;       //Kepala Depo

		//boti
		$map[ '2' ][ '1' ] = 100;
		$map[ '2' ][ '2' ] = 100;
		$map[ '2' ][ '3' ] = 35;
		$map[ '2' ][ '4' ] = 35;
		$map[ '2' ][ '5' ] = 35;
		$map[ '2' ][ '6' ] = 35;
		$map[ '2' ][ '7' ] = 35;
		$map[ '2' ][ '8' ] = 35;
		$map[ '2' ][ '9' ] = 35;
		$map[ '2' ][ '10' ] = 35;
		$map[ '2' ][ '11' ] = 100;
		$map[ '2' ][ '12' ] = 35;
		$map[ '2' ][ '13' ] = 100;
		$map[ '2' ][ '14' ] = 100;
		$map[ '2' ][ '15' ] = 35;
		$map[ '2' ][ '16' ] = 100;
		$map[ '2' ][ '17' ] = 100;
		$map[ '2' ][ '18' ] = 100;
		$map[ '2' ][ '19' ] = 100;
		$map[ '2' ][ '20' ] = 100;
		$map[ '2' ][ '21' ] = 100;
		$map[ '2' ][ '22' ] = 35;


		$returnValue = isset( $map[ $pg ][ $role ] ) ? $map[ $pg ][ $role ] : 0;
		return $returnValue;
	}
}

?>
