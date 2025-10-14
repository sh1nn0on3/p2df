<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * P2DF Email Forensic System
     * 1. Create users with RSA keys
     * 2. Create sample encrypted emails
     */
    public function run(): void
    {
        echo "\n";
        echo "====================================\n";
        echo "  P2DF Email Forensic System Seed  \n";
        echo "====================================\n\n";

        // Seed users first (with RSA key generation)
        $this->call([
            UserSeeder::class,
        ]);

        // Then seed sample emails (encrypted with Admin's public key)
        $this->call([
            SampleEmailSeeder::class,
        ]);

        echo "\n";
        echo "====================================\n";
        echo "  Database seeding completed!      \n";
        echo "====================================\n";
        echo "\n";
        echo "Login credentials:\n";
        echo "  Admin: admin@example.com / password\n";
        echo "  Investigator 1: inv1@example.com / password\n";
        echo "  Investigator 2: inv2@example.com / password\n";
        echo "\n";
    }
}
