<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Api extends CI_Controller {

    const ALLOW = 'ALLOW';
    const DISALLOW = 'DISALLOW';
    const ANONYMOUS = [
        'id' => 0,
        'uname' => 'api@client.authorize',
        'role' => 'anonymous',
    ];
    const AUTHENTICATED = [
        'id' => 7,
        'uname' => 'fadil@xcoder.developer',
        'role' => 'authorized',
    ];
    const URI_PREFIX = 'https://reqres.in/api/users/';
	
	public function __construct()
    {
        parent::__construct();
        $this->publicKey = file_get_contents(APPPATH . '/keys/api-public.pem');
        $this->privateKey = file_get_contents(APPPATH . '/keys/api-private.pem');
        
        # Added to bypass CORS
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization");
        header('Content-Type: application/json');

        // Timezone
        date_default_timezone_set("Indian/Mauritius");
    }

    # Disable gateway
    public function index() {
        exit;
    }

    # Client requesting JWT and sending response
    public function requestJwt()
    {
        $request_access = $this->input->post('request_access');
        
        if (!$request_access) {
            echo json_encode([
                'HTTP' => 'Request denied !',
            ]);

            return;
        }
        
        $tokenData['author'] = 'fadil@xcoder.developer';
        $tokenData['timeStamp'] = Date("l jS \of F Y h:i:s A");

        echo json_encode([
            'jwt' => JWT::encode($tokenData, $this->privateKey, 'RS256'),
        ]);
	}

    # Verify & decode JWT
    public function decodeJwt()
    {
        try
        {
            $received_Token = $this->input->post('signature');
            $jwtData = JWT::decode($received_Token, new Key($this->publicKey, 'HS256'));

            if (self::ANONYMOUS !== (array)$jwtData) {
                http_response_code('401');
                echo json_encode(array( "status" => false, "message" => 'INVALID'));
                exit;
            }
            
            echo json_encode([
                'bearer' => 'Bearer ' . JWT::encode(self::AUTHENTICATED, $this->privateKey, 'RS256'),
            ]);
        }
        catch (Exception $e)
        {
            http_response_code('401');
            echo json_encode(array( "status" => false, "message" => $e->getMessage()));
            exit;
        }
    }

    public function apiQueryVerifier()
    {   
        try
        {
            if ($_SERVER['REQUEST_METHOD'] == "OPTIONS")
                return 0;

            $auth = $this->input->request_headers('Authorization');
            $authBearer = $auth['authorization'] ?? ($auth['Authorization'] ?? null);

            if (null === $authBearer)
                return;
        
            $authBearer = explode(' ', $authBearer);
            $encContent = $authBearer[1];
            $decContent = JWT::decode((string)$encContent, new Key($this->publicKey, 'RS256'));

            if (self::AUTHENTICATED !== (array)$decContent) {
                http_response_code('401');
                echo json_encode(array( "status" => false, "message" => 'INVALID'));
                return;
            }

            // Set up and execute the curl process
            $curl_handle = curl_init();
            if ($curl_handle === false) {
                throw new Exception('failed to initialize');
            }
            curl_setopt($curl_handle, CURLOPT_URL, self::URI_PREFIX . rand(1, 12));
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($curl_handle, CURLOPT_POST, 1);
            // curl_setopt($curl_handle, CURLOPT_POSTFIELDS, array(
            //     'name' => 'name',
            //     'email' => 'example@example.com'
            // ));

            // Optional, delete this line if your API is open
            // curl_setopt($curl_handle, CURLOPT_USERPWD, $username . ':' . $password);

            $content = curl_exec($curl_handle);
            
            if ($content === false) {
                throw new Exception(curl_error($curl_handle), curl_errno($curl_handle));
            }
            
            curl_close($curl_handle);
            $result = json_decode($content);
            
            echo json_encode([
                'http_status' => '200',
                'data' => $result->data,
                'support' => $result->support,
            ]);
        }
        catch (Exception $e)
        {
            http_response_code('401');
            echo json_encode(array( "status" => false, "message" => $e->getMessage()));
        }
    }

    public function usersLising()
    {
        $this->load->database();

        if(isset($_POST['users']))
        {
            // $this->db->truncate('vip_list');
            $arr = [];

            foreach($_POST['users'] as $users)
            {
                $arr[] = [
                    'name' => $users[0],
                    'surname' => $users[1],
                    'email' => $users[2],
                    'registered_at' => $users[3],
                    'qa' => $users[4],
                    'status' => ($users[5] === 'true') ? true : false,
                    'net_worth' => $users[6],
                ];
            }

            $this->db->insert('api_users_list', ['data' => json_encode($arr) ]);
        }

        
        
        $query = $this->db->from('api_users_list')->order_by('id','desc')->limit(1)->get()->row();
        $response = json_decode($query->data);
        $result = [];
        
        foreach ($response as $row)
        {
            $result[] = [
                $row->name,
                $row->surname,
                $row->email,
                $row->registered_at,
                $row->qa,
                $row->status,
                $row->net_worth,
            ];
        }

        echo json_encode([
            'response' =>  $result
        ]);
    }
}
