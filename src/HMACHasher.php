<?php

namespace Magic;

class HMACHasher
{
    private static string $defaultAlgorithm;

    public function __construct(private string $sharedKey, private ?string $algorithm = null)
    {
        $algorithm = $this->algorithm ?? self::$defaultAlgorithm;

        self::verifyAlgorithm($algorithm);

        $this->algorithm = $algorithm;
    }

    public static function setDefaultAlgorithm(string $algorithm)
    {
        self::verifyAlgorithm($algorithm);
        self::$defaultAlgorithm = $algorithm;
    }

    private static function verifyAlgorithm(string $algorithm)
    {
        if (! in_array($algorithm, hash_hmac_algos())) {
            throw new \RuntimeException('Unknown algorithm');
        }
    }

    public function __invoke(string $data)
    {
        return hash_hmac($this->algorithm, $data, $this->sharedKey);
    }
}
