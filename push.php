<?php  

//deviceToken,设备Token,需要替换你设备的
$deviceToken = 'fc766c03b0857fb08e14ecdde3bdfef80f24030cb955dd5804fe143bbc944665';  

//p12.pem文件的密码，需要替换你设置的
$passphrase = '654321';  

//推送的消息
$message = '这是一条豪冷的消息^_^';  

////////////////////////////////////////////////////////////////////////////////  

$ctx = stream_context_create();  
stream_context_set_option($ctx, 'ssl', 'local_cert', 'cp.pem'); //cp.pem 需要替换为你生成的
//stream_context_set_option($ctx, 'ssl', 'cafile', 'entrust_2048_ca.cer'); // 需要 entrust_2048_ca.cer 证书时，打开此句代码
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);  

// Open a connection to the APNS server  
// 上架：ssl://gateway.push.apple.com:2195 
// 测试：ssl://gateway.sandbox.push.apple.com:2195
$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);  

if (!$fp)  exit("Failed to connect: $err $errstr" . PHP_EOL);  
else echo 'Connected to APNS' . PHP_EOL;  

// Create the payload body  
$body['aps'] = array(  
'alert' => $message,  
'sound' => 'default'  
);  

// Encode the payload as JSON  
$payload = json_encode($body);  

// Build the binary notification  
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;  

// Send it to the server  
$result = fwrite($fp, $msg, strlen($msg));  

if (!$result)  echo 'Message not delivered' . PHP_EOL;  
else  echo 'Message successfully delivered' . PHP_EOL;  

// Close the connection to the server  
fclose($fp);  
?>  