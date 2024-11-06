<?php
include_once 'vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Token {
    public static function createToken($email, $id) {
        $payload = array(
            "email" => $email,
            "id" => $id,
            "iat" => time(),
            "exp" => time() + 3600
        );
        return JWT::encode($payload, "llavesupersecreta010203", "HS256");
    }

    public static function decode($token) {
        return JWT::decode($token, new Key("llavesupersecreta010203", "HS256"));
    }

    public static function validateToken($token) {
        try {
            $decoded = self::decode($token);
            return [
                "valid" => true,
                "data" => $decoded
            ];
        } catch (\Exception $e) {
            return [
                "valid" => false,
                "error" => $e->getMessage()
            ];
        }
    }

   
}