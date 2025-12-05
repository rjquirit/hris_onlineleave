<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use RuntimeException;

class EncryptionService
{
    /**
     * Encrypt a value using the configured passcode.
     *
     * @param  mixed  $value
     */
    public function encrypt($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $passcode = Config::get('encryption.passcode');

        if (empty($passcode)) {
            throw new RuntimeException('Encryption passcode not configured. Please set ENCRYPTION_PASSCODE in your .env file.');
        }

        $algorithm = Config::get('encryption.algorithm', 'AES-256-CBC');

        // Generate a random initialization vector
        $ivLength = openssl_cipher_iv_length($algorithm);
        $iv = openssl_random_pseudo_bytes($ivLength);

        // Derive encryption key from passcode
        $key = hash('sha256', $passcode, true);

        // Encrypt the value
        $encrypted = openssl_encrypt(
            (string) $value,
            $algorithm,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encrypted === false) {
            throw new RuntimeException('Encryption failed.');
        }

        // Combine IV and encrypted data, then base64 encode
        return base64_encode($iv.$encrypted);
    }

    /**
     * Decrypt a value using the configured passcode.
     */
    public function decrypt(?string $encryptedValue): ?string
    {
        if ($encryptedValue === null || $encryptedValue === '') {
            return null;
        }

        $passcode = Config::get('encryption.passcode');

        if (empty($passcode)) {
            throw new RuntimeException('Encryption passcode not configured. Please set ENCRYPTION_PASSCODE in your .env file.');
        }

        $algorithm = Config::get('encryption.algorithm', 'AES-256-CBC');

        // Decode the base64 encoded value
        $data = base64_decode($encryptedValue, true);

        if ($data === false) {
            throw new RuntimeException('Invalid encrypted data format.');
        }

        // Extract IV and encrypted data
        $ivLength = openssl_cipher_iv_length($algorithm);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        // Derive decryption key from passcode
        $key = hash('sha256', $passcode, true);

        // Decrypt the value
        $decrypted = openssl_decrypt(
            $encrypted,
            $algorithm,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false) {
            throw new RuntimeException('Decryption failed.');
        }

        return $decrypted;
    }

    /**
     * Generate a blind index for searchable encryption.
     * This allows LIKE queries on encrypted data.
     */
    public function generateBlindIndex(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $algorithm = Config::get('encryption.blind_index.algorithm', 'sha256');
        $key = Config::get('encryption.blind_index.key');

        if (empty($key)) {
            throw new RuntimeException('Blind index key not configured.');
        }

        // Normalize the value for consistent searching
        $normalizedValue = mb_strtolower(trim($value));

        // Generate HMAC-based hash for the blind index
        return hash_hmac($algorithm, $normalizedValue, $key);
    }

    /**
     * Generate multiple blind indexes for partial matching (LIKE %value%).
     * Creates indexes for substrings to enable partial search.
     *
     * @param  int  $minLength  Minimum substring length for indexing
     */
    public function generatePartialBlindIndexes(?string $value, int $minLength = 3): array
    {
        if ($value === null || $value === '' || mb_strlen($value) < $minLength) {
            return [];
        }

        $indexes = [];
        $normalizedValue = mb_strtolower(trim($value));
        $length = mb_strlen($normalizedValue);

        // Generate indexes for all substrings of minimum length
        for ($i = 0; $i <= $length - $minLength; $i++) {
            for ($len = $minLength; $len <= $length - $i; $len++) {
                $substring = mb_substr($normalizedValue, $i, $len);
                $indexes[] = $this->generateBlindIndex($substring);
            }
        }

        return array_unique($indexes);
    }

    /**
     * Search for a value in blind indexes.
     * Generates the blind index for the search term.
     */
    public function searchIndex(string $searchTerm): string
    {
        return $this->generateBlindIndex($searchTerm);
    }
}
