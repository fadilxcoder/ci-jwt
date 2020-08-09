<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/CreatorJwt.php';

class Welcome extends CI_Controller {

	
	public function __construct()
    {
        parent::__construct();
        $this->objOfJwt = new CreatorJwt();
        
        # Added to bypass CORS
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization");
        header('Content-Type: application/json');
        
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method == "OPTIONS") {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization");
            header("HTTP/1.1 200 OK");
        }
    }

    # GET Request
    public function generateToken()
    {
        # Data to be encoded as JWT signature
        $tokenData['dev'] = 'fadil@xcoder.dvlpr';
		$tokenData['timeStamp'] = Date('Y-m-d h:i:s');
    		
		$jwtToken = $this->objOfJwt->GenerateToken($tokenData);
    		
	    echo json_encode([
	        'Token' => $jwtToken,
	        'serverResp' =>  $this->input->get('var'),
        ]);
	        
	}
     
    # GET Request with Authorization header
    public function verifyToken()
    {
        $received_Token = $this->input->request_headers('Authorization');
    	$header = $received_Token['authorization'];
    	$auth = explode(' ', $header);
        $type = $auth[0];
        $token = $auth[1];

        try
        {
            // $jwtData = $this->objOfJwt->DecodeToken($received_Token['Authorization']);
            $jwtData = $this->objOfJwt->DecodeToken($token);
            echo json_encode([
            	'response' => $jwtData, # Decoded JWT signature to return <dev> & <timeStamp>
            	'id' => rand(1,99),
            	'type' => $type,
            	'token' => $token,
        	]);
        }
        catch (Exception $e)
        {
            http_response_code('401');
            echo json_encode(array( "status" => false, "message" => $e->getMessage()));exit;
        }
    }
    
    public function _404()
	{
		$this->output->set_status_header('404');
	}
	
}
