<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Email;
use App\Models\User;
use App\Models\ForensicReport;

class AdminReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo báo cáo mẫu cho admin test
        $email = Email::first();
        $investigator = User::where('role', 'investigator')->first();
        
        if ($email && $investigator) {
            ForensicReport::create([
                'email_id' => $email->id,
                'investigator_id' => $investigator->id,
                'title' => 'Bao cao dieu tra email nghi ngo',
                'severity' => 'high',
                'findings' => 'Phat hien noi dung email co dau hieu bat thuong, chua cac tu khoa lien quan den hoat dong bat hop phap.',
                'analysis' => 'Phan tich cho thay email nay co the lien quan den hoat dong rua tien. Can dieu tra sau hon ve nguon goc va muc dich.',
                'recommendations' => "1. Thu thap them bang chung tu cac nguon khac\n2. Phoi hop voi co quan chuc nang\n3. Theo doi cac giao dich lien quan",
                'related_logs' => ['Log 1: Email duoc tao luc 10:30', 'Log 2: Duoc truy cap boi investigator'],
                'status' => 'completed',
                'completed_at' => now()
            ]);
            
            $this->command->info('Created sample report for admin testing');
        }
    }
}

