<?php

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function generateJWT($payload) {
    $header = json_encode([
        'typ' => 'JWT',
        'alg' => 'HS256'
    ]);

    $base64UrlHeader = base64UrlEncode($header);
    $base64UrlPayload = base64UrlEncode(json_encode($payload));

    $secret = 'your_secret_key_here';
    
    $signature = hash_hmac('sha256', 
        $base64UrlHeader . "." . $base64UrlPayload, 
        $secret, 
        true
    );
    
    $base64UrlSignature = base64UrlEncode($signature);

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function verifyJWT($token) {
    $secret = 'your_secret_key_here';
    
    $tokenParts = explode('.', $token);
    if (count($tokenParts) != 3) {
        return false;
    }

    $header = base64_decode($tokenParts[0]);
    $payload = base64_decode($tokenParts[1]);
    $signatureProvided = $tokenParts[2];

    $base64UrlHeader = base64UrlEncode($header);
    $base64UrlPayload = base64UrlEncode($payload);

    $signature = hash_hmac('sha256', 
        $base64UrlHeader . "." . $base64UrlPayload, 
        $secret, 
        true
    );
    
    $base64UrlSignature = base64UrlEncode($signature);

    $payloadData = json_decode($payload, true);
    
    if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
        return false;
    }

    return $signatureProvided === $base64UrlSignature;
}