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
            "exp" => time() + (30 * 24 * 60 * 60)
        );
        return JWT::encode($payload, "llavesupersecreta010203", "HS256");
    }

    public static function decode($token) {
        $token = str_replace('Bearer ', '', $token);
        return JWT::decode($token, new Key("llavesupersecreta010203", "HS256"));
    }

    public static function validateToken($token) {
        try {
            $decoded = self::decode($token);
            $id = $decoded->id;
            $email = $decoded->email;

            return [
                "valid" => true,
                "id" => $id,
                "email" => $email
            ];
        } catch (\Exception $e) {
            return [
                "valid" => false,
                "error" => $e->getMessage()
            ];
        }
    }

   
}