<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CryptoService;
use Illuminate\Console\Command;

class GenerateUserKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keys:generate 
                            {--user= : Generate keys for specific user email}
                            {--all : Generate keys for all users without keys}
                            {--force : Regenerate keys even if they exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate RSA key pairs for users (Admin or Investigator)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cryptoService = new CryptoService();
        
        // Tạo thư mục lưu keys nếu chưa có
        $keysDir = storage_path('keys');
        if (!is_dir($keysDir)) {
            mkdir($keysDir, 0755, true);
            $this->info("Created keys directory: {$keysDir}");
        }

        $userEmail = $this->option('user');
        $generateAll = $this->option('all');
        $force = $this->option('force');

        if ($userEmail) {
            // Generate keys for specific user
            $user = User::where('email', $userEmail)->first();
            
            if (!$user) {
                $this->error("User with email '{$userEmail}' not found.");
                return 1;
            }

            $this->generateKeysForUser($user, $cryptoService, $keysDir, $force);
        } elseif ($generateAll) {
            // Generate keys for all users without keys
            $users = User::all();
            
            $generated = 0;
            foreach ($users as $user) {
                $publicKeyPath = storage_path($user->public_key_path);
                
                // Skip if keys exist and not forcing
                if (!$force && $user->public_key_path && file_exists($publicKeyPath)) {
                    $this->line("Skipping {$user->email}: Keys already exist");
                    continue;
                }

                if ($this->generateKeysForUser($user, $cryptoService, $keysDir, $force)) {
                    $generated++;
                }
            }

            $this->info("\nGenerated keys for {$generated} user(s).");
        } else {
            // Find users without keys - check file existence in PHP
            $usersNeedingKeys = [];
            foreach (User::all() as $user) {
                if (!$user->public_key_path || !file_exists(storage_path($user->public_key_path))) {
                    $usersNeedingKeys[] = $user;
                }
            }

            if (empty($usersNeedingKeys)) {
                $this->info("All users have valid keys.");
                return 0;
            }

            $this->info("Found " . count($usersNeedingKeys) . " user(s) without valid keys:");
            foreach ($usersNeedingKeys as $user) {
                $this->line("  - {$user->email} ({$user->role})");
            }

            if (!$this->confirm('Do you want to generate keys for these users?')) {
                return 0;
            }

            foreach ($usersNeedingKeys as $user) {
                $this->generateKeysForUser($user, $cryptoService, $keysDir, false);
            }
        }

        return 0;
    }

    /**
     * Generate RSA keys for a specific user
     */
    private function generateKeysForUser(User $user, CryptoService $cryptoService, string $keysDir, bool $force): bool
    {
        try {
            // Check if keys already exist
            if (!$force && $user->public_key_path && file_exists(storage_path($user->public_key_path))) {
                $this->warn("Skipping {$user->email}: Keys already exist. Use --force to regenerate.");
                return false;
            }

            $this->info("Generating keys for: {$user->email} ({$user->role})...");

            // Generate key name based on user email or role
            $keyName = $user->isAdmin() 
                ? 'admin' 
                : 'inv' . $user->id . '_' . str_replace(['@', '.'], '_', $user->email);

            // Generate RSA key pair
            $keys = $cryptoService->generateRsaKeyPair($keysDir, $keyName);

            // Update user with key paths
            $user->update([
                'public_key_path' => str_replace(storage_path(), '', $keys['public_key_path']),
                'private_key_path' => str_replace(storage_path(), '', $keys['private_key_path']),
            ]);

            $this->info("✓ Keys generated successfully:");
            $this->line("  Public key:  {$user->public_key_path}");
            $this->line("  Private key: {$user->private_key_path}");

            return true;
        } catch (\Exception $e) {
            $this->error("Failed to generate keys for {$user->email}: " . $e->getMessage());
            return false;
        }
    }
}

