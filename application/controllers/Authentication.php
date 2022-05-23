<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

use chriskacerguis\RestServer\RestController;

class Authentication extends RestController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('AuthenticationModel');
        date_default_timezone_set("Asia/Kolkata"); // Setting time zone of India.
    }

    public function index_get() {
        echo "I am RESTful API";
    }

    public function signinWithPhonenumber_post() {

        // Takes raw data from the request
        $requestJSON = json_decode(file_get_contents("php://input"));

        // Converts it into a PHP object
        $requestDATA = json_decode($requestJSON);

        // Destructuring data
        $phoneNumber = $requestDATA['phone_number'];

        if(isset($phoneNumber)) {

            // clean data
            $phoneNumber = $this->clean($phoneNumber);

            // generate OTP
            $code = $this->generateCode();

            // update otp in database
            $this->updateCode($phoneNumber, $code);

            // send sms to the given mobile number
            $this->sendSMS($phoneNumber, $code);

            // Check whether a user exists or not.
            $isUserExist = $this->userExists($phoneNumber);

            if($isUserExist) {

                $this->response([
                    'status' => true,
                    'message' => 'A verification code has been sent to your mobile number '. $phoneNumber
                ], RestController::HTTP_OK);

            } else {

                // If user doesn't exists create user in database
                $result = $this->createNewUser($phoneNumber);

                if($result > 0) {

                    $this->response([
                        'status' => true,
                        'message' => 'A verification code has been sent to your mobile number '. $phoneNumber
                    ], RestController::HTTP_OK);

                } else {
                    $this->response([
                        'status' => false,
                        'message' => 'Sorry, we had some technical problems during last opeartion. Please try again.'
                    ], RestController::HTTP_INTERNAL_ERROR);
                }
            }

        } else {
            $this->response([
                'status' => false,
                'message' => 'Mobile number you entered is invalid or empty. Please check the number and enter a correct phone number.'
            ], RestController::HTTP_BAD_REQUEST); 
        }

    }

    // Create new user function.
    private function createNewUser($phoneNumber)
    {
        $authModel = new AuthenticationModel;

        $data = [
            'uid' => $this->createUniqueId(),
            'phone_number' => $this->encode($phoneNumber),
            'created' => date('Y-m-d h:i:s'),
            'updated' => date('Y-m-d h:i:s')
        ];

        $result = $authModel->create_user($phoneNumber);
        if($result > 0) return true;
        else return false;
    }

    // Update code in database
    private function updateCode($phoneNumber, $code)
    {
        $authModel = new AuthenticationModel;

        $data = [
            'phone_number' => $this->encode($phoneNumber),
            'code' => $this->encode($code),
            'created' => date('Y-m-d h:i:s')
        ];

        $result = $authModel->update_code($data);
        if($result > 0) return true;
        else return false;
    }

    // Check whether a user exists or not.
    private function userExists($phoneNumber) 
    {
        $authModel = new AuthenticationModel;

        $result = $authModel->user_exists($phoneNumber);
        if($result > 0) return true;
        else return false;
    }

    # send SMS function
    private function sendSMS($phoneNumber, $code) {

        $msgBody = urlencode("Use $code as your OTP to verify your Mobile number for luxtrivia.in");

        $url = "https://sms.vrinfosoft.co.in/unified.php?usr=28282&pwd=123456&ph=$phoneNumber&sndr=LUXOTI&text=$msgBody";

        // init the resource 
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET'
        ));

       $response = curl_exec($curl);
       curl_close($curl);
    }

    # generate code function
    private function generateCode()
    {
        $OTP = rand(1, 9);
        $OTP .= rand(0, 9);
        $OTP .= rand(0, 9);
        $OTP .= rand(0, 9);
        $OTP .= rand(0, 9);
        $OTP .= rand(0, 9);
        return $OTP;
    }

    # sanitize form data function
    private function clean($stringToSanitize)
    {
        $stringToSanitize = htmlspecialchars($stringToSanitize);
        $stringToSanitize = stripslashes($stringToSanitize);
        $stringToSanitize = trim($stringToSanitize);
        return $stringToSanitize;
    }

    # encode string
    private function encode($string)
    {
        return 'ENCODE('. $string .')';
    }

    # create uniqueId without initial
    private function createUniqueId() 
	{
		return rand(10,90).strtoupper(uniqid()).rand(111,999);
	}

    # create uniqueId with initial
    private function createUniqueIdWithInitial($initial) 
	{
		return $initial.strtoupper(uniqid()).rand(111,999);
	}

}    