<?php

require ('vendor/autoload.php');
use Aws\S3\S3Client as S3Client;
use Aws\Exception\AwsException;



class Aws3
{
	private $s3client;

	private static $configuration =
		array(
			"biznetstorage-dms"=>array(
                'version' => 'latest',
                'region'  => 'us-east-1',
                'endpoint' => 'https://nos.jkt-1.neo.id',
                'use_path_style_endpoint' => true,
                'credentials' => array(
                    'key'    => "00f09b2ef8a7fe82b89f",
                    'secret' => "uhZ/imtB4pzXzbZMC62h3Z5VYIviH53QDPQ/2qNB",
                )),

			"storage_1"=>array(
                'version' => 'latest',
                'region'  => 'us-east-1',
                'endpoint' => 'https://storage1.pitjarus.co',
                'use_path_style_endpoint' => true,
                'credentials' => array(
                    'key'    => "EFU8LBKKYHQRC6INJCWE",
                    'secret' => "QBzQaszTEUzwFcFxzBDbFjpqOiZMa5EVq1O24eQO",
                )),
                

			"storage_2"=> array(
				'version' => 'latest',
                'region'  => 'us-east-1',
                'endpoint' => 'https://storage2.pitjarus.co',
                'use_path_style_endpoint' => true,
                'credentials' => array(
                    'key'    => "IUYC1JRQC7RHREZJ2NXS",
                    'secret' => "aTYkDKkfm9lj6Vz4RUmB3b5Jh+ZSR5yxcBYuOVlr",
                )
			),
			
			"storage_3"=> array(
				'version' => 'latest',
                'region'  => 'us-east-1',
                'endpoint' => 'https://storage3.pitjarus.co',
                'use_path_style_endpoint' => true,
                'credentials' => array(
                    'key'    => "E2LLDL7SJQXRH4OF2XF8",
                    'secret' => "S83nyY3pPF9cmy2D5IrKisnNJoCVnwzwm04hmuR0",
                )
			),
			"storage_4"=> array(
				'version' => 'latest',
                'region'  => 'us-east-1',
                'endpoint' => 'https://storage4.pitjarus.co',
                'use_path_style_endpoint' => true,
                'credentials' => array(
                    'key'    => "pitMinio",
                    'secret' => "PitjarusMinio2020!",
                )
			),
			"storage_5"=> array(
				'version' => 'latest',
                'region'  => 'us-east-1',
                'endpoint' => 'https://storage5.pitjarus.co',
                'use_path_style_endpoint' => true,
                'credentials' => array(
                    'key'    => "pitMinio",
                    'secret' => "PitjarusMinio2020!",
                )
			),
			"storage_9"=> array(
				'version' => 'latest',
                'region'  => 'us-east-1',
                'endpoint' => 'https://storage9.pitjarus.co',
                'use_path_style_endpoint' => true,
                'credentials' => array(
                    'key'    => "pitMinio",
                    'secret' => "PitjarusMinio2020!",
                )
			),
			"storage_99"=> array(
				'version' => 'latest',
				'region'  => 'us-east-1',
				'endpoint' => 'https://nos.jkt-1.neo.id',
				'use_path_style_endpoint' => true,
				'credentials' => array(
					'key'    => "00f09b2ef8a7fe82b89f",
					'secret' => "uhZ/imtB4pzXzbZMC62h3Z5VYIviH53QDPQ/2qNB",
				)
			)
		);

 public function __construct($configNumber=4)
    {     
		// $key = "storage_".$configNumber;
		$key = "biznetstorage-dms";
        $this->s3client = new S3Client(
         Aws3::$configuration[$key]
        );
    }
	
	public function getBuckets()
	{
		$awsResult = $this->s3client->listBuckets();
		$buckets = $awsResult->get("Buckets");
		$metadata = $awsResult->get("@metadata");
		$result = array(
			"Buckets"=>$buckets,
			"metadata"=>$metadata
		);

		return $result;
	}

	public function listObjects($bucketName)
	{
        $awsResult = $this->s3client->listObjectsV2(array("Bucket"=>$bucketName));
		$contents = $awsResult->get("Contents");
        $metadata = $awsResult->get("@metadata");

        $result = array(
        	"Bucket"=>$bucketName,
        	"Contents" => $contents,
			"metadata"=>$metadata
		);

        return $result;
    }

	public function createBucket($bucketName)
	{
		$result = array(

			"result_create" => (array) $this->s3client->createBucket(
                array(
                    "ACL"=>"public-read",
                    "Bucket"=>$bucketName
                )
            ),

			"result_set_policy"=> (array)$this->putPolicyReadOnly($bucketName)
		);


		return $result;
	}

	private function putPolicyReadOnly($bucketName)
	{
        $policyReadOnly = '{
		  "Version": "2012-10-17",
		  "Statement": [
			{
			  "Action": [
				"s3:GetBucketLocation",
				"s3:ListBucket"
			  ],
			  "Effect": "Allow",
			  "Principal": {
				"AWS": [
				  "*"
				]
			  },
			  "Resource": [
				"arn:aws:s3:::%s"
			  ],
			  "Sid": ""
			},
			{
			  "Action": [
				"s3:GetObject"
			  ],
			  "Effect": "Allow",
			  "Principal": {
				"AWS": [
				  "*"
				]
			  },
			  "Resource": [
				"arn:aws:s3:::%s/*"
			  ],
			  "Sid": ""
			}
		  ]
		}
		';

        $result = $this->s3client->putBucketPolicy(
        	array(
        		"Bucket"=>$bucketName,
				"Policy"=>sprintf($policyReadOnly,$bucketName,$bucketName)
			)
		);

        return $result;
	}

	public function putObject($bucketName,$folders, $fileName, $file, $mimeType = "application/octet-stream")
	{

		$awsResult = $this->s3client->putObject(
			array(
                'Bucket' => $bucketName,
                'Key'    => ($folders !== "")? $folders."/".$fileName : $fileName,
                'SourceFile'   => $file["tmp_name"],
                'ACL'    => 'public-read',
				'ContentType'=>$mimeType
			)
		);
		$metadata = $awsResult->get("@metadata");
		$objectURL = $awsResult->get("ObjectURL");

		$result = array(
			"ObjectURL"=>$objectURL,
			"metadata"=>$metadata['statusCode']==200

		);

		return $result;
	}
}

?>
