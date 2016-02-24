<?php

require '../vendor/autoload.php';

$app = new \Slim\Slim(array(
 'debug' => true,
 'mode' => 'development',
 'log.enabled' => true,
 'cookies.encrypt' => true,
));
$app->contentType('application/json');
$app->get('/', function(){
  echo json_encode(array('test' => true));
});
$app->post('/sendme/:email', function($email){
  try{
    $filename = $_FILES['file']['name'];
    $fileext= strrchr($filename, '.');
    $newfilename = basename($filename, $fileext). rand(1111,9999);
    $destination = 'tmp/'. $newfilename. $fileext;
    move_uploaded_file($_FILES['file']['tmp_name'], $destination);
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'mx1.hostinger.com.br';
    $mail->SMTPAuth = true;
    $mail->Username = 'sendme@nocoffeenocode.com';
    $mail->Password = '';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->From      = 'sendme@nocoffeenocode.com';
    $mail->FromName  = 'SendMe';
    $mail->Subject   = 'Message Sent Via SendMe';
    $mail->Body      = 'Hi, your file sent by SendMe is served';
    $mail->AddAddress( $email );
    $mail->AddAttachment( $destination, $filename );
    $sendRet = $mail->Send();
    unlink($destination);
    echo json_encode(array('status' => true, 'file' => $destination, 'sendRet' => $sendRet));
  }catch(Exception $ex){
    echo json_encode(array('status' => false, 'reason' => $ex->getMessage()));
  }
});
$app->options('/(:x+)', function() use ($app) {
$app->response()->setStatus(200);
});
$app->run();
