<?php  

class NafathMoudle{

    protected $BaseUrl;

    protected $sandbox = true;

    protected $dummy = true;

    public function __construct() {

        $this->sandbox  = !empty( get_option( '_nafath_sandbox' )) ? get_option( '_nafath_sandbox' ) : '';
        $this->dummy    = !empty( get_option( '_nafath_dummy' )) ? get_option( '_nafath_dummy' ) : false ;
        $this->apikey   = !empty(get_option( '_nafath_apikey' )) ? get_option( '_nafath_apikey' ) : '77ba6d15-16ea-4e82-9427-39b12917b0c7';

        // Production api url .
        $this->BaseUrl = 'https://iamservices.semati.sa/';

        if( $this->sandbox ){
            // Sandbox api url .
            $this->BaseUrl = 'https://test-iamservices.semati.sa/';
        }
    }
    
	public function credential(){
		
		$credential = array(
			'X-IBM-Client-Id: ' . get_option( '_client_id' ),
			'X-IBM-Client-Secret: ' . get_option( '_client_secret' ),
		  );

        return $credential;
    }

    public function do_request( $id )
    {
        $url = $this->BaseUrl;
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url . 'nafath/api/v1/client/authorize/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{"id": "' . $id . '","action": "SpRequest","service": "Login"}',
        CURLOPT_HTTPHEADER => array(
            'Authorization: apikey ' . $this->apikey,
            'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        
        $data = [];

		$data = json_decode($response);
 
        return $data;   
    }
    
    public function login( $nafath_id ){

        if( empty( $nafath_id ) ) {
            return;
        }

        $response = $this->do_request( $nafath_id );

        return $response;
    }
  
}