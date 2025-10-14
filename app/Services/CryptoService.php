<?php

namespace App\Services;

use Exception;

/**
 * CryptoService - Xử lý mã hóa và giải mã cho P2DF Email Forensic
 * 
 * Sử dụng:
 * - AES-256-CBC cho mã hóa nội dung email (symmetric encryption)
 * - RSA-2048 cho mã hóa AES key (asymmetric encryption)
 * 
 * @package App\Services
 */
class CryptoService
{
    /**
     * Thuật toán mã hóa AES
     */
    private const AES_CIPHER = 'AES-256-CBC';

    /**
     * Độ dài IV cho AES (16 bytes = 128 bits)
     */
    private const AES_IV_LENGTH = 16;

    /**
     * Thuật toán RSA padding
     */
    private const RSA_PADDING = OPENSSL_PKCS1_OAEP_PADDING;

    /**
     * Mã hóa dữ liệu bằng AES-256-CBC
     * 
     * @param string $plaintext Văn bản gốc cần mã hóa
     * @param string $aesKey Khóa AES (32 bytes cho AES-256)
     * @return string Chuỗi mã hóa dạng: base64(IV + encrypted_data)
     * @throws Exception
     */
    public function aesEncrypt(string $plaintext, string $aesKey): string
    {
        // Kiểm tra độ dài khóa AES (phải là 32 bytes cho AES-256)
        if (strlen($aesKey) !== 32) {
            throw new Exception('AES key must be 32 bytes for AES-256');
        }

        // Tạo IV ngẫu nhiên (16 bytes)
        $iv = openssl_random_pseudo_bytes(self::AES_IV_LENGTH);

        // Mã hóa dữ liệu
        $encrypted = openssl_encrypt(
            $plaintext,
            self::AES_CIPHER,
            $aesKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encrypted === false) {
            throw new Exception('AES encryption failed: ' . openssl_error_string());
        }

        // Ghép IV + encrypted_data và encode base64
        // Format: base64(IV || encrypted_data)
        return base64_encode($iv . $encrypted);
    }

    /**
     * Giải mã dữ liệu đã được mã hóa bằng AES-256-CBC
     * 
     * @param string $encryptedData Dữ liệu đã mã hóa (base64)
     * @param string $aesKey Khóa AES (32 bytes)
     * @return string Văn bản gốc đã giải mã
     * @throws Exception
     */
    public function aesDecrypt(string $encryptedData, string $aesKey): string
    {
        // Kiểm tra độ dài khóa AES
        if (strlen($aesKey) !== 32) {
            throw new Exception('AES key must be 32 bytes for AES-256');
        }

        // Decode base64
        $data = base64_decode($encryptedData);
        if ($data === false) {
            throw new Exception('Failed to decode base64 encrypted data');
        }

        // Tách IV và encrypted_data
        $iv = substr($data, 0, self::AES_IV_LENGTH);
        $encrypted = substr($data, self::AES_IV_LENGTH);

        // Giải mã
        $decrypted = openssl_decrypt(
            $encrypted,
            self::AES_CIPHER,
            $aesKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false) {
            throw new Exception('AES decryption failed: ' . openssl_error_string());
        }

        return $decrypted;
    }

    /**
     * Mã hóa dữ liệu bằng RSA Public Key
     * 
     * @param string $data Dữ liệu cần mã hóa (thường là AES key)
     * @param string $publicKeyPath Đường dẫn tới file public key (.pem)
     * @return string Dữ liệu đã mã hóa (base64)
     * @throws Exception
     */
    public function rsaEncrypt(string $data, string $publicKeyPath): string
    {
        // Đọc public key từ file
        if (!file_exists($publicKeyPath)) {
            throw new Exception("Public key file not found: {$publicKeyPath}");
        }

        $publicKey = file_get_contents($publicKeyPath);
        $key = openssl_pkey_get_public($publicKey);

        if ($key === false) {
            throw new Exception('Invalid public key: ' . openssl_error_string());
        }

        // Mã hóa dữ liệu
        $encrypted = '';
        $result = openssl_public_encrypt(
            $data,
            $encrypted,
            $key,
            self::RSA_PADDING
        );

        openssl_free_key($key);

        if (!$result) {
            throw new Exception('RSA encryption failed: ' . openssl_error_string());
        }

        return base64_encode($encrypted);
    }

    /**
     * Giải mã dữ liệu đã được mã hóa bằng RSA Private Key
     * 
     * @param string $encryptedData Dữ liệu đã mã hóa (base64)
     * @param string $privateKeyPath Đường dẫn tới file private key (.pem)
     * @param string|null $passphrase Mật khẩu bảo vệ private key (nếu có)
     * @return string Dữ liệu đã giải mã
     * @throws Exception
     */
    public function rsaDecrypt(string $encryptedData, string $privateKeyPath, ?string $passphrase = null): string
    {
        // Đọc private key từ file
        if (!file_exists($privateKeyPath)) {
            throw new Exception("Private key file not found: {$privateKeyPath}");
        }

        $privateKey = file_get_contents($privateKeyPath);
        $key = openssl_pkey_get_private($privateKey, $passphrase ?? '');

        if ($key === false) {
            throw new Exception('Invalid private key or passphrase: ' . openssl_error_string());
        }

        // Decode base64
        $encrypted = base64_decode($encryptedData);
        if ($encrypted === false) {
            throw new Exception('Failed to decode base64 encrypted data');
        }

        // Giải mã
        $decrypted = '';
        $result = openssl_private_decrypt(
            $encrypted,
            $decrypted,
            $key,
            self::RSA_PADDING
        );

        openssl_free_key($key);

        if (!$result) {
            throw new Exception('RSA decryption failed: ' . openssl_error_string());
        }

        return $decrypted;
    }

    /**
     * Sinh AES key ngẫu nhiên (32 bytes cho AES-256)
     * 
     * @return string AES key (32 bytes)
     * @throws Exception
     */
    public function generateAesKey(): string
    {
        $key = openssl_random_pseudo_bytes(32, $strong);

        if (!$strong) {
            throw new Exception('Failed to generate strong AES key');
        }

        return $key;
    }

    /**
     * Sinh cặp RSA key pair (public & private)
     * 
     * @param string $outputDir Thư mục lưu key
     * @param string $keyName Tên file key (không có extension)
     * @param int $keySize Độ dài key (default: 2048 bits)
     * @return array ['public_key_path' => string, 'private_key_path' => string]
     * @throws Exception
     */
    public function generateRsaKeyPair(string $outputDir, string $keyName, int $keySize = 2048): array
    {
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Tạo config file tạm thời (cần cho Windows)
        $tempConfig = $this->createTempOpenSSLConfig($outputDir);
        
        // Cấu hình sinh key
        $configArgs = [
            'private_key_bits' => $keySize,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'config' => $tempConfig,
        ];

        // Sinh key pair
        $resource = openssl_pkey_new($configArgs);
        
        if ($resource === false) {
            throw new Exception('Failed to generate RSA key pair: ' . openssl_error_string());
        }

        // Export private key
        $exported = openssl_pkey_export($resource, $privateKey, null, $configArgs);
        
        if (!$exported) {
            throw new Exception('Failed to export private key: ' . openssl_error_string());
        }

        // Export public key
        $keyDetails = openssl_pkey_get_details($resource);
        if (!$keyDetails || !isset($keyDetails['key'])) {
            throw new Exception('Failed to get public key details');
        }
        $publicKey = $keyDetails['key'];

        // Đường dẫn lưu file
        $publicKeyPath = $outputDir . '/' . $keyName . '_public.pem';
        $privateKeyPath = $outputDir . '/' . $keyName . '_private.pem';

        // Lưu key vào file
        file_put_contents($publicKeyPath, $publicKey);
        file_put_contents($privateKeyPath, $privateKey);

        // Set quyền cho private key (skip trên Windows vì không hỗ trợ chmod đúng cách)
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            chmod($privateKeyPath, 0600);
        }

        openssl_free_key($resource);

        return [
            'public_key_path' => $publicKeyPath,
            'private_key_path' => $privateKeyPath,
        ];
    }

    /**
     * Tạo file OpenSSL config tạm thời (fix cho Windows)
     * 
     * @param string $dir Directory to store temp config
     * @return string Path to temp config file
     */
    private function createTempOpenSSLConfig(string $dir): string
    {
        $configPath = $dir . '/openssl_temp.cnf';
        
        $configContent = <<<EOT
# OpenSSL configuration for key generation
[ req ]
default_bits = 2048
distinguished_name = req_distinguished_name

[ req_distinguished_name ]
EOT;

        file_put_contents($configPath, $configContent);
        
        return $configPath;
    }

    /**
     * Tạo hash SHA-256 cho dữ liệu (dùng để verify integrity)
     * 
     * @param string $data Dữ liệu cần hash
     * @return string Hash (hex)
     */
    public function hash(string $data): string
    {
        return hash('sha256', $data);
    }

    /**
     * Verify hash SHA-256
     * 
     * @param string $data Dữ liệu gốc
     * @param string $hash Hash cần kiểm tra
     * @return bool True nếu hash khớp
     */
    public function verifyHash(string $data, string $hash): bool
    {
        return hash_equals($this->hash($data), $hash);
    }
}

