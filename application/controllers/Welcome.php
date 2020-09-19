<?php defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class Welcome extends CI_Controller {

    const ALLOW = 'ALLOW';
    const DISALLOW = 'DISALLOW';
	
	public function __construct()
    {
        parent::__construct();
        $this->publicKey = file_get_contents(APPPATH . '/keys/public.pem');
        $this->privateKey = file_get_contents(APPPATH . '/keys/private.pem');
        
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

    # Verify CLIENT KEY and send response identifier
    public function index()
    {
        $publicKey = $this->input->post('PUBLIC_KEY');
        
        // if (null == $publicKey) {
        //     // $this->_404();
        //     echo json_encode([
        //         'HTTP' => '404',
        //     ]);

        //     return;
        // }

        // var_dump(($this->publicKey));
        // var_dump(($publicKey));

        $this->publicKey = str_replace('-----BEGIN PUBLIC KEY-----', '', $this->publicKey);
        $this->publicKey = str_replace('-----END PUBLIC KEY-----', '', $this->publicKey);
        var_dump($this->publicKey);

        $publicKey = str_replace('-----BEGIN PUBLIC KEY-----', '', $publicKey);
        $publicKey = str_replace('-----END PUBLIC KEY-----', '', $publicKey);
        var_dump($publicKey);
        die;
        
        if ($this->publicKey == $publicKey) {
            echo json_encode([
                'AUTHORIZATION_GRANT' => self::ALLOW,
            ]);
        } else {
            echo json_encode([
                'AUTHORIZATION_GRANT' => self::DISALLOW,
            ]);
        }
        
    }

    # Verify CLIENT KEY and send token
    public function generateToken()
    {
        if ($this->_isAuthorized()) :
            # Data to be encoded as JWT signature
            $tokenData['dev'] = 'fadil@xcoder.dvlpr';
            $tokenData['timeStamp'] = Date('Y-m-d h:i:s');
            $tokenData['var'] = $this->input->get('var');
                
            $jwtToken = JWT::encode($tokenData, $this->privateKey, 'RS256');
                
            echo json_encode([
                'Token' => $jwtToken,
                'serverResp' =>  $this->input->get('var'),
            ]);
        endif;
	}
     
    # Verify CLIENT KEY and decode token
    public function verifyToken()
    {
        if ($this->_isAuthorized()) :
            $received_Token = $this->input->request_headers('Authorization');
            $header = $received_Token['authorization'] ?? $received_Token['Authorization'];
            $auth = explode(' ', $header);
            $type = $auth[0];
            $token = $auth[1];
            
            /* 
            * Code use to detect Algorithm used
            */
            // $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJkZXYiOiJmYWRpbEB4Y29kZXIuZHZscHIiLCJ0aW1lU3RhbXAiOiIyMDIwLTA5LTEzIDAyOjMzOjU5In0.HV5wKLMK7elWLMPugD_SGU1hSLFDoICSMGpjMx2gpeb2E5_E6OWR1NAHMvdaaPLFBWK7nxfZSkfjJWglvJ6BGgi8B6kyx9GL0Db5R1zarMSlePbR1LemyTw1MDcT3WpUKSnf9c5CiOryARJQzoVL-4fKHTTBIHyiY4JX5688nmI';
            // $tks = explode('.', $jwt);       
            // list($headb64, $bodyb64, $cryptob64) = $tks;
            // $header = \Firebase\JWT\JWT::jsonDecode(Firebase\JWT\JWT::urlsafeB64Decode($headb64));
            // var_dump($header->alg);
            // die;
            

            try
            {
                $jwtData = JWT::decode($token, $this->publicKey, array('RS256'));
                
                echo json_encode([
                    'response' => $jwtData, # Decoded JWT signature to return <dev> & <timeStamp>
                    'id' => rand(1,99),
                    'type' => $type,
                    'var' => $jwtData->var,
                ]);
            }
            catch (Exception $e)
            {
                http_response_code('401');
                echo json_encode(array( "status" => false, "message" => $e->getMessage()));exit;
            }
        endif;
    }
    
    # Check CLIENT KEY response identifier
    private function _isAuthorized()
    {
        $AUTH = $this->input->post('AUTHORIZATION_GRANT');
        
        if (null == $AUTH || $AUTH == self::DISALLOW) {
            echo json_encode([
                'AUTHORIZATION_GRANT' => self::DISALLOW,
            ]);

            return;
        }

        return true;
    }
	
}
