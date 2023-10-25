<?php
/**
 * Template Name: Nafathcallback Page
 */

 $data = [];
 $NafathHeaders = [];
 $entityBody = file_get_contents('php://input');
 if( class_exists('WC_Logger') ) {
     $log = new WC_Logger(); 
     $log->add('Nafath_2', 'NafathBOdy: ' .$entityBody);
     $log->add('Nafath_2', 'NafathHeaders: ' . print_r(getallheaders(), true));
 }

 $data = [];

if( ! empty($entityBody) ) {

     $nafath_respons = json_decode( $entityBody );
     $response       = explode('.', $nafath_respons->response);
     $userInfo       = im_urlsafeB64Decode($response[1]);
     $userInfo       = json_decode($userInfo);

     $data['userInfo'] = $userInfo->userInfo;
     $data['response'] = $nafath_respons->response;
     $data['transId']  = $nafath_respons->transId;
     $data['cardId']   = isset($userInfo->userInfo->id) ? $userInfo->userInfo->id : '';
     $data['status']   = $nafath_respons->status;
    
     $NafathDB = new NafathDB();
    
     $NafathDB->update_nafath_callback($data);
 }

 echo 'nafazcallback-page';