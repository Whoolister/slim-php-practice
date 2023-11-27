<?php
declare(strict_types=1);

namespace App\application\services;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;
use function gethostname;
use function sha1;
use function time;

class JwtService
{
    private const SECRET_KEY = 'L4C0M4ND4';
    private const ENCRYPTION_TYPE = 'HS256';

    /**
     * Creates a JWT with the given data
     *
     * @param array $data The data to encode in the JWT
     * @return string The encoded JWT
     */
    public function createToken(array $data): string
    {
        return JWT::encode(
            payload: [
                'aud' => $this->getAUD(),
                'iat' => time(),
                'nbf' => time(),
                'exp' => time() + 60 * 60 * 24,
                'data' => $data,
                'app' => 'La Comanda'
            ],
            key: self::SECRET_KEY,
            alg: self::ENCRYPTION_TYPE
        );
    }

    /**
     * Verifies that the given JWT is valid
     *
     * @param string $token The JWT to verify
     * @return bool Whether the token is valid or not
     * @throws Exception If the token is empty
     */
    public function verifyToken(string $token): bool
    {
        if (empty($token)) {
            throw new Exception('Token vacío', 400);
        }

        try {
            $decoded = JWT::decode(
                $token,
                new Key(self::SECRET_KEY, self::ENCRYPTION_TYPE)
            );
        } catch (Exception $e) {
            throw new Exception('Token inválido', 401, $e);
        }

        return $decoded->aud === $this->getAUD();
    }

    /**
     * Decodes the given JWT and returns its payload
     *
     * @param string $token The JWT to decode
     * @return stdClass The payload of the JWT
     * @throws Exception If the token is empty
     */
    public function getPayload(string $token): stdClass
    {
        if (empty($token)) {
            throw new Exception('Token vacío', 400);
        }

        return JWT::decode(
            $token,
            new Key(self::SECRET_KEY, self::ENCRYPTION_TYPE),
        );
    }

    /**
     * @return string The AUD claim for the JWT
     */
    public function getAUD(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}