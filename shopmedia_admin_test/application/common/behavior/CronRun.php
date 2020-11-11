<?php
namespace app\common\behavior;

use think\Exception;
use think\Response;

class CronRun
{

    public function run(&$dispatch){
        $host_name = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "*";
        $headers = [
            "Access-Control-Allow-Origin" => $host_name,
            "Access-Control-Allow-Credentials" => 'true',
            "Access-Control-Allow-Headers" => "x-token,x-uid,x-token-check,x-requested-with,content-type,Host,sign, version, model, apptype, did, user-id, access-user-token, admin-user-id, admin-user-token,commonheader",
            "Access-Control-Allow-Methods" => "PUT,DELETE,POST,GET"
        ];
        if($dispatch instanceof Response) {
            $dispatch->header($headers);
        } else if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $dispatch['type'] = 'response';
            $response = new Response('', 200, $headers);
            $dispatch['response'] = $response;

        }
    }

}