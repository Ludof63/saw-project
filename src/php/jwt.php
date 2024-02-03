<?php

declare(strict_types=1);
require_once(__DIR__ . "/../php/imports.php");

class JWT
{
    private static function encode_payload(array $payload): string
    {
        $json = json_encode($payload);
        check($json !== false);
        return JWT::encode_string($json);
    }

    private static function decode_payload(string $json): array
    {
        $payload = json_decode(JWT::decode_string($json), true);
        if (!is_array($payload)) throw new InvalidJWTException();
        return $payload;
    }

    private static function encode_string(string $string): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }

    private static function decode_string(string $string): string
    {
        $decoded = base64_decode(str_replace(['-', '_', ''], ['+', '/', '='], $string));
        if ($decoded === false) throw new InvalidJWTException();
        return $decoded;
    }

    private static function sign(string $payload): string
    {
        $signature = hash_hmac('sha256', $payload, get_config("jwt_secret"), true);
        check($signature !== false);
        return JWT::encode_string($signature);
    }

    /**
     * @return array{string,string,string}
     */
    private static function explode(string $jwt): array
    {
        $result = explode(".", $jwt);
        if (count($result) !== 3) throw new InvalidJWTException();
        return $result;
    }

    private static function from_payload(array $payload): JWT
    {
        return new JWT(["typ" => "JWT", "alg" => "HS256"], $payload);
    }

    public static function from_id(int $id): JWT
    {
        return JWT::from_payload(["iat" => time(), "sub" => $id]);
    }

    /**
     * @return array{JWT,string}
     */
    public static function from_string(string $string): array
    {
        $pieces = JWT::explode($string);
        return [new JWT(JWT::decode_payload($pieces[0]), JWT::decode_payload($pieces[1])), $pieces[2]];
    }

    private function __construct(array $header, array $payload)
    {
        $this->header = $header;
        $this->payload = $payload;
    }

    private array $header;
    private array $payload;

    private function encode(): string
    {
        $header = JWT::encode_payload($this->header);
        $payload = JWT::encode_payload($this->payload);
        return "$header.$payload";
    }

    public function __toString(): string
    {
        $encoded = $this->encode();
        $signature = JWT::sign($encoded);
        return "$encoded.$signature";
    }

    private function check_signature(string $signature): bool
    {
        return JWT::sign($this->encode()) === $signature;
    }

    private function check_lifetime(): bool
    {
        if (!array_key_exists("iat", $this->payload)) return false;
        $iat = $this->payload["iat"];
        return  is_int($iat) && $iat > time() - TOKEN_LIFESPAN;
    }

    public function is_valid(DB $db, string $signature): bool
    {
        return $this->check_signature($signature) && $this->check_lifetime() && !is_jwt_invalid($db, $signature);
    }

    public function get_id(): int
    {
        $sub = get($this->payload, "sub");
        if (!is_int($sub)) throw new InvalidJWTException();
        return $sub;
    }

    public function get_iat(): int
    {
        $iat = get($this->payload, "iat");
        if (!is_int($iat)) throw new InvalidJWTException();
        return $iat;
    }
}
