<?php
namespace App\Services;


use NZTim\Mailchimp\Mailchimp;
use Illuminate\Support\Facades\Log;

class TornadoCoreService {


    var $autorization = "";

    private function httpPost($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if($this->autorization != "")
        {
            Log::alert($this->autorization);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $this->autorization  ));
        }

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    private function jwt_request($url,$post) {

        $token = $this->autorization;
        header('Content-Type: application/json'); // Specify the type of data
        $ch = curl_init($url); // Initialise cURL
        $post = json_encode($post); // Encode the data array into a JSON string
        $authorization = "Authorization: Bearer ".$token; // Prepare the authorisation token
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
        $result = curl_exec($ch); // Execute the cURL statement
        curl_close($ch); // Close the cURL connection
        return json_decode($result); // Return the received data

    }

    public function login()
    {
        $r = $this->httpPost('https://tornadocore.ivanpastorsimarro.com/api/auth/login',
            [
                'email' => env('TORNADOCORE_USER'),
                'password' => env('TORNADOCORE_PASSWORD'),
            ]);

        $login_data = json_decode($r);
        $this->autorization = $login_data->access_token;

    }

    public function checkBSResult($ip)
    {
        $data = [
            'ip' => $ip
        ];

        $response = $this->jwt_request('https://tornadocore.ivanpastorsimarro.com/api/security/checkBSResult',$data);

        return $response;
    }

    public function addBSResult($ip,$resultado)
    {
        $data  = [
            'ip' => $ip,
            'result' => $resultado,
        ];

        $response = $this->jwt_request('https://tornadocore.ivanpastorsimarro.com/api/security/addBSResult',$data);

        return $response;
    }



}
