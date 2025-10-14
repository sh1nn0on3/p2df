<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Services\CryptoService;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Tạo:
     * - 1 Admin
     * - 2 Investigator
     * Và sinh RSA key pair cho mỗi user
     */
    public function run(): void
    {
        $cryptoService = new CryptoService();

        // Tạo thư mục lưu keys nếu chưa có
        $keysDir = storage_path('keys');
        if (!is_dir($keysDir)) {
            mkdir($keysDir, 0755, true);
        }

        // ========================
        // Admin User
        // ========================
        echo "Creating Admin user...\n";
        
        // Sinh RSA key pair cho Admin
        $adminKeys = $cryptoService->generateRsaKeyPair($keysDir, 'admin');
        
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'public_key_path' => str_replace(storage_path(), '', $adminKeys['public_key_path']),
            'private_key_path' => str_replace(storage_path(), '', $adminKeys['private_key_path']),
        ]);

        echo "✓ Admin created: {$admin->email} / password\n";
        echo "  Public key: {$admin->public_key_path}\n";
        echo "  Private key: {$admin->private_key_path}\n\n";

        // ========================
        // Investigator 1
        // ========================
        echo "Creating Investigator 1...\n";
        
        $inv1Keys = $cryptoService->generateRsaKeyPair($keysDir, 'investigator1');
        
        $inv1 = User::create([
            'name' => 'Investigator One',
            'email' => 'inv1@example.com',
            'password' => Hash::make('password'),
            'role' => 'investigator',
            'public_key_path' => str_replace(storage_path(), '', $inv1Keys['public_key_path']),
            'private_key_path' => str_replace(storage_path(), '', $inv1Keys['private_key_path']),
        ]);

        echo "✓ Investigator 1 created: {$inv1->email} / password\n";
        echo "  Public key: {$inv1->public_key_path}\n";
        echo "  Private key: {$inv1->private_key_path}\n\n";

        // ========================
        // Investigator 2
        // ========================
        echo "Creating Investigator 2...\n";
        
        $inv2Keys = $cryptoService->generateRsaKeyPair($keysDir, 'investigator2');
        
        $inv2 = User::create([
            'name' => 'Investigator Two',
            'email' => 'inv2@example.com',
            'password' => Hash::make('password'),
            'role' => 'investigator',
            'public_key_path' => str_replace(storage_path(), '', $inv2Keys['public_key_path']),
            'private_key_path' => str_replace(storage_path(), '', $inv2Keys['private_key_path']),
        ]);

        echo "✓ Investigator 2 created: {$inv2->email} / password\n";
        echo "  Public key: {$inv2->public_key_path}\n";
        echo "  Private key: {$inv2->private_key_path}\n\n";

        echo "==================================\n";
        echo "Users seeded successfully!\n";
        echo "==================================\n";
    }
}
