<?php
// 参考ページ: http://kenzo0107.hatenablog.com/entry/2016/04/28/003655
class LineBotApi {
    const CHANNEL_ID     = '1469904360';
    const CHANNEL_SECRET = 'cadb3352a866e7811c1a5d8d655e3f91';
    const MID            = 'u48d6abf59024909b4a3eae290539188e'; 
    
    private $requestContent;
    public function __construct() {
        $requestBodyString = file_get_contents('php://input');
        $requestBodyObject = json_decode($requestBodyString);
 
       $this->requestContent = $requestBodyObject->result{0}->content;
    }
    public function main() {
        // Line上で入力した文字
        $requestText = $this->requestContent->text;
        // Line上で入力したコンテンツタイプ(1:テキスト)
        $contentType = $this->requestContent->contentType;

	//debug
	ob_start();
	var_dump($requestBodyObject->result{0}->content);
	$result =ob_get_contents();
	ob_end_clean();

	$fp = fopen("./dump.txt", "a+" );
	fputs($fp, $result);
	fclose( $fp );

       $message = self::getMessage($requestText);
        
	// Line Bot Api 実行.
        self::executeLineBotApi($message);
    }
    private function getMessage($requestText) {
        if (empty($requestText)) {
            return;
        }
        return $requestText;
    }
    // LINE BOT API へのリクエストを作成して実行
    private function executeLineBotApi($message) {
        
        if (empty($message)) {
            return;
        }
        $headers = self::getHeaders();
        error_log('$headers:' . print_r($headers,true));
        $requestFrom = $this->requestContent->from;
        $responseMessage = <<< EOM
{
  "to":["{$requestFrom}"],
  "toChannel":1383378250,
  "eventType":"138311608800106203",
  "content":{
    "contentType":1,
    "toType":1,
    "text":"{$message}"
  }
}
EOM;
        $curl = curl_init('https://trialbot-api.line.me/v1/events');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $responseMessage);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
    }
    // line bot api 送信用 headers
    private function getHeaders() {
        return array(
            "Content-Type: application/json; charset=UTF-8",
            "X-Line-ChannelID: ".self::CHANNEL_ID,
            "X-Line-ChannelSecret: ".self::CHANNEL_SECRET,
            "X-Line-Trusted-User-With-ACL: ".self::MID,
        );
    }
}
// api実行
$LineBotApi = new LineBotApi();
$LineBotApi->main();
