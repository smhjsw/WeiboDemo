<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Util\LogUtil;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
class ChatController extends Controller
{
    public function prompt(Request $request)
    {
        $content = $request->get("content");
        $apiKey = $request->get("api_key");
        /// TODO:
        /// 这里先限制 1000 个字符
        /// 实际 gpt-4 限制是 8192 tokens
        if (mb_strlen($content) > 1000) {
            return [
                "success" => false,
                "message" => "内容太长"
            ];
        }

        $url = 'https://api.openai.com/v1/chat/completions';
        
        $data = array(
            'model' => 'gpt-4',
            'messages' => array(
                array('role' => 'user', 'content' => $content)
            )
        );

        $jsonData = json_encode($data);

        try {
            $client = new Client();
            $response = $client->post($url, [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'json' => $jsonData,
            ]);
            $response = json_decode($response->getBody());

            if (isset($response->error->message)) {
                LogUtil::logToWechat("ChatGPT 请求异常\n{$response->error->message}", ['key' => '06a7187b-b5db-4baa-80a0-b7633e851ee7']);
                return [
                    "success" => false,
                    "message" => "请求失败，请稍候再试"
                ];
            }

            $reply = $response->choices[0]->message->content;

            return [
                "success" => true,
                "content" => $reply
            ];
        } catch (ConnectException $e) {
            if ($e->getMessage()) {
                LogUtil::logToWechat("ChatGPT 请求异常\n{$e->getMessage()}", ['key' => '06a7187b-b5db-4baa-80a0-b7633e851ee7']);
            }
            return [
                "success" => false,
                "message" => "请求失败，请稍候再试"
            ];
        }
    }
}
