<?php

class RegaMoudle{

    protected $BaseUrl;

    protected $sandbox = true;

    protected $dummy = true;


    public function __construct() {
        $this->sandbox  = get_option( '_sandbox' );
        $this->dummy    = get_option( '_dummy' );

        // Production api url .
        $this->BaseUrl = 'https://integration-gw.nhc.sa/nhc/prod/';

        if( $this->sandbox ){
            // Sandbox api url .
            $this->BaseUrl = 'https://integration-gw.housingapps.sa/nhc/dev/';
        }
    }
    
	public function credential(){
		
		$credential = array(
			'X-IBM-Client-Id: ' . get_option( '_client_id' ),
			'X-IBM-Client-Secret: ' . get_option( '_client_secret' ),
            'RefId: ' . get_option( '_client_id' ),
            'CallerReqTime: ' . strtotime( date('Y-m-d') ) ,
		);

        return $credential;
    }

    public function do_request($url='', $type, $endpoint, $headers = array(), $body = array() , $params = array() )
    {
        if( empty($url) ) {
            $url = $this->BaseUrl . $endpoint;
            $url .= !empty( $params ) ? '?'.http_build_query($params) : '';
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLINFO_HEADER_OUT => true,
		  CURLOPT_CUSTOMREQUEST => $type,
          CURLOPT_POSTFIELDS => $body,
		  CURLOPT_HTTPHEADER => $headers,
		));

        $response = curl_exec($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        curl_close($curl);

        $data = [];
		
        if( $this->dummy && $endpoint === 'v1/brokerage/AdvertisementValidator' ) {
            if ($httpcode == '200') {
                $data = $this->valid_response();
            // local test only
            } elseif( $httpcode == '403' ){  $data = $this->valid_response();  
            } elseif( $httpcode == '401' ){  $data = $this->valid_response(); 
            } else {
                // $response =  json_decode($response , true);
                $data = $this->valid_response();
            }
        }elseif ( $httpcode == '403' ){
            $data = $response; 
        }else{
            $data = $response;
        }
        
        return $data;
        
    }
    
    public function AdvertisementValidator( $adLicenseNumber =''){

        $userID    = get_current_user_id();
        $id_number = get_the_author_meta( '_im_type_id' , $userID );
        $type_id   = get_the_author_meta( '_im_user_type' , $userID );

        $response = $this->do_request(
            '',
            'GET',
            'v1/brokerage/AdvertisementValidator',
            $this->credential(),
            array(),
            [
                'adLicenseNumber' => $adLicenseNumber,
                'advertiserId'    => $id_number,
                'idType'          => $type_id,
            ]
        );

        // $response = $this->test_response();
        return $response;
    }

    public function CreateADLicense( $bodyData = array() )
    {
        $testUrl = 'https://integration-gw.housingapps.sa/nhc/dev/v1/brokerage/CreateADLicense';
        $response = $this->do_request(
            $testUrl,
            'POST',
            'v1/brokerage/CreateADLicense',
            array(
                'X-IBM-Client-Id: 7170eb897cb971a3a35a55a887121d42',
                'X-IBM-Client-Secret: 7bd077b49b8238ef23c6ee05215cf9f7',
                'RefId: 7170eb897cb971a3a35a55a887121d42',
                'CallerReqTime: ' . strtotime( date('Y-m-d') ),
              ),
            json_encode($bodyData),
            array(),
        );

        return $response;
    }

    public function SendAttachment( $bodyData = array() )
    {
        $testUrl = 'https://integration-gw.housingapps.sa/nhc/dev/v1/brokerage/SendAttachment';
        $response = $this->do_request(
            $testUrl,
            'POST',
            'v1/brokerage/SendAttachment',
            array(
                'X-IBM-Client-Id: 7170eb897cb971a3a35a55a887121d42',
                'X-IBM-Client-Secret: 7bd077b49b8238ef23c6ee05215cf9f7',
                'RefId: 7170eb897cb971a3a35a55a887121d42',
                'CallerReqTime: ' . strtotime( date('Y-m-d') ),
              ), 
            $bodyData,
            array(),
        );

        return $response;
    }
    

  
    public function rega_errors( $response ){
        $errors = [];

        if( isset( $response ['errors'] ) ){
            foreach ( $response ['errors'] as $error_type) {
                foreach ($error_type as $error_msg) {
                    $errors[] = $error_msg;
                }
            }
    
        }

        if( isset( $response ['errorCode'] ) ){
            $errors[] = $response['errorMsg_AR'];
        }

        if($response !== true && empty( $errors ) ){
            $errors[] = 'هنالك مشكلة في الاتصال مع هيئة العقار';
        }

        return $errors ;
    }

    public function valid_response()
    {
        return '{
            "Header":{
               "ResTime":"2023-05-30T10:02:19.357Z",
               "ChId":"realestateportals",
               "ReqID":"adc3e9106475c9a9002e6c10",
               "Status":{
                  "Code":200,
                  "Description":"OK"
               }
            },
            "Body":{
               "result":{
                  "isValid":true,
                  "advertisement":{
                     "advertiserId":"1034758704",
                     "adLicenseNumber":"7100000031",
                     "deedNumber":311010000240,
                     "advertiserName":"خالد بن عبدالعزيز بن عبدالرحمن المحسن",
                     "phoneNumber":"0583727427",
                     "brokerageAndMarketingLicenseNumber":"1100000139",
                     "isConstrained":false,
                     "isPawned":false,
                     "streetWidth":0,
                     "propertyArea":3000,
                     "propertyPrice":300,
                     "numberOfRooms":0,
                     "propertyType":"أرض",
                     "propertyAge":"جديد",
                     "advertisementType":"إيجار",
                     "location":{
                        "region":"منطقة الحدود الشماليه",
                        "regionCode":"9",
                        "city":"طريف",
                        "cityCode":"901030",
                        "district":"العزيزية",
                        "districtCode":"901030",
                        "street":"dd",
                        "postalCode":"56456",
                        "buildingNumber":"5645",
                        "additionalNumber":"5555",
                        "longitude":"42.666667",
                        "latitude":"30.0"
                     },
                     "propertyFace":"شرقية",
                     "planNumber":"15468-5",
                     "obligationsOnTheProperty":"لايوجد",
                     "guaranteesAndTheirDuration":null,
                     "theBordersAndLengthsOfTheProperty":"50x100 MQ",
                     "complianceWithTheSaudiBuildingCode":null,
                     "channels": [                       
                            "منصة مرخصة",
                            "لوحة اعلانية"  
                    ],
                    "propertyUsages": [                  
                            "زراعي"                   
                    ],
                    "propertyUtilities": [                     
                            "مياه"                    
                    ],
                     "creationDate":"31/06/2023",
                     "endDate":"15/07/2023",
                     "qrCodeUrl":"https://test-brokerage.housingapps.sa/public/IndividualBroker/ElanDetails/08db0361-4dc7-414a-8a75-6b1b9e971160"
                  }
               },
               "targetUrl":null,
               "success":true,
               "error":null,
               "unAuthorizedRequest":false,
               "__abp":true
            }
         }
        ';
    }

    public function error_response()
    {
        return '{
            "Header":{
               "ResTime":"2023-05-30T10:05:10.055Z",
               "ChId":"realestateportals",
               "ReqID":"adc3e9106475ca55002e7570",
               "Status":{
                  "Code":400,
                  "Description":"Bad Request"
               }
            },
            "Body":{
               "result":null,
               "targetUrl":null,
               "success":false,
               "error":{
                  "code":400,
                  "message":"رقم الهوية / السجل التجاري غير مرتبط برقم تصريح الإعلان العقاري المدخل _IV0003",
                  "details":null,
                  "validationErrors":null
               },
               "unAuthorizedRequest":false,
               "__abp":true
            }
         }
        ';
    }
}