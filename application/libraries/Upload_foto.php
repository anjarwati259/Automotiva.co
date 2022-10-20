<?php

class Upload_foto {

	function uploadFileToServer($parent_folder,$nama_file,$file)
	{
		$aws3 = new Aws3(99);

		$result = $aws3->putObject("sariroti",$parent_folder,$nama_file.".jpg",$file,"image/jpeg");
		if($result["metadata"]){
			return $result["ObjectURL"];
		}
		else{
			return "";
		}
	}

	function upload_photo_minio( $param = array() ){
		$return_array = array();

		if ( !empty( $_FILES[ $param[ 'element' ] ] ) ) {

			if ( $_FILES[ $param[ 'element' ] ][ 'size' ] > 2000000 ) {
				$return_array[ 'status' ] = FALSE;
				$return_array[ 'error_message' ] = 'Size foto tidak bisa lebih dari 2 MB.';
				return $return_array;
			}


			$file = $_FILES[ $param[ 'element' ] ];
			$ext_arr = explode( '.' , $file[ 'name' ] );
			$ext = end( $ext_arr );
			$fileName = $param[ 'file_name' ] . '.' . $ext;

			if ( !in_array(strtolower($ext), array('pdf','jpg','jpeg','png', 'bmp')) ) {
				$return_array[ 'status' ] = FALSE;
				$return_array[ 'error_message' ] = 'Format File tidak mendukung';
				return $return_array;
			}

			$aws3 = new Aws3(99);

			$up0 = $aws3->putObject("sariroti",$param['parent_folder'],$param['file_name'].".jpg",$file,"image/jpeg");

			if ( !$up0['metadata'] ) {
				$return_array[ 'status' ] = FALSE;
				$return_array[ 'error_message' ] = 'Failed to upload AWS3 Server.';
			} else {
				$return_array[ 'status' ] = TRUE;
				$return_array[ 'file_name' ] =  $param[ 'file_name' ] . '.' . $ext;
				$return_array[ 'file_name_aws' ] = $up0['ObjectURL'];
			}
		} else {
			$return_array[ 'status' ] = FALSE;
			$return_array[ 'error_message' ] = 'File tidak dapat dibaca oleh sistem';
		}

		return $return_array;

	}


}

?>
