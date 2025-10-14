<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Email;
use App\Models\User;
use App\Services\CryptoService;

class SampleEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Tạo một số email mẫu đã được mã hóa
     */
    public function run(): void
    {
        $cryptoService = new CryptoService();

        // Lấy Admin user để lấy public key
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            echo "Error: Admin user not found. Please run UserSeeder first.\n";
            return;
        }

        $adminPublicKeyPath = storage_path($admin->public_key_path);

        if (!file_exists($adminPublicKeyPath)) {
            echo "Error: Admin public key not found.\n";
            return;
        }

        echo "Creating sample emails...\n\n";

        // Danh sách email mẫu
        $sampleEmails = [
            [
                'from' => 'john.doe@company.com',
                'to' => 'jane.smith@company.com',
                'subject' => 'Quarterly Report Review',
                'body' => "Hi Jane,\n\nPlease review the attached quarterly report. We need to discuss the Q3 results in tomorrow's meeting.\n\nBest regards,\nJohn",
            ],
            [
                'from' => 'alice.wang@tech.com',
                'to' => 'bob.jones@tech.com',
                'subject' => 'Project Alpha - Security Concerns',
                'body' => "Bob,\n\nI've identified several security vulnerabilities in Project Alpha. We need to address these issues before the release.\n\nDetailed report attached.\n\nAlice",
            ],
            [
                'from' => 'mike.brown@corp.com',
                'to' => 'sarah.lee@corp.com',
                'subject' => 'Confidential: Merger Discussion',
                'body' => "Sarah,\n\nThis is highly confidential. The board has approved moving forward with the merger talks. Please keep this information strictly private.\n\nMike",
            ],
            [
                'from' => 'lisa.chen@startup.io',
                'to' => 'team@startup.io',
                'subject' => 'Team Update - New Funding Round',
                'body' => "Team,\n\nGreat news! We've successfully closed our Series A funding round at $10M. This will help us scale faster.\n\nLet's celebrate this Friday!\n\nLisa",
            ],
            [
                'from' => 'david.kim@finance.com',
                'to' => 'audit@finance.com',
                'subject' => 'Suspicious Transaction Alert',
                'body' => "Audit Team,\n\nWe've detected suspicious transactions in account #4582. Please investigate immediately.\n\nTransaction IDs: TX-9871, TX-9872, TX-9873\n\nDavid Kim\nFraud Detection Team",
            ],
        ];

        foreach ($sampleEmails as $index => $emailData) {
            try {
                // Sinh AES key ngẫu nhiên
                $aesKey = $cryptoService->generateAesKey();

                // Mã hóa nội dung email
                $bodyEncrypted = $cryptoService->aesEncrypt($emailData['body'], $aesKey);

                // Mã hóa AES key bằng public key của Admin
                $aesKeyEncryptedAdmin = $cryptoService->rsaEncrypt($aesKey, $adminPublicKeyPath);

                // Tạo hash
                $hash = $cryptoService->hash($emailData['body']);

                // Lưu vào database
                $email = Email::create([
                    'from' => $emailData['from'],
                    'to' => $emailData['to'],
                    'subject' => $emailData['subject'],
                    'body_encrypted' => $bodyEncrypted,
                    'aes_key_encrypted_admin' => $aesKeyEncryptedAdmin,
                    'hash' => $hash,
                ]);

                echo "✓ Email #{$email->id} created: {$emailData['subject']}\n";

            } catch (\Exception $e) {
                echo "✗ Error creating email #{$index}: {$e->getMessage()}\n";
            }
        }

        echo "\n==================================\n";
        echo "Sample emails seeded successfully!\n";
        echo "==================================\n";
    }
}
