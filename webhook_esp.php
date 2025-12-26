<?php
// Webhook simples para receber dados do ESP
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (isset($data['mac'], $data['qrcode'])) {
        // Reenviar para Laravel
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/esp/presenca');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        echo $response ?: '{"status":"success","message":"Processado"}';
    } else {
        echo '{"status":"error","message":"Dados inválidos"}';
    }
} else {
    echo '{"status":"error","message":"Método não permitido"}';
}
?>