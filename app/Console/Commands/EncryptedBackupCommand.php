<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Donation;
use App\Models\Elder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class EncryptedBackupCommand extends Command
{
    protected $signature = 'backups:encrypted';

    protected $description = 'Create an encrypted snapshot of critical records';

    public function handle(): int
    {
        $diskName = config('backup.disk', 's3_backups');
        $disk = Storage::disk($diskName);

        $payload = [
            'generated_at' => now()->toIso8601String(),
            'users' => User::select(['id', 'name', 'email', 'branch_id', 'created_at'])->get(),
            'elders' => Elder::select(['id', 'first_name', 'last_name', 'branch_id', 'priority_level'])->get(),
            'donations' => Donation::with('elder')
                ->latest('created_at')
                ->take(2000)
                ->get()
                ->map(fn (Donation $donation) => [
                    'id' => $donation->id,
                    'user_id' => $donation->user_id,
                    'elder_id' => $donation->elder_id,
                    'amount' => $donation->amount,
                    'currency' => $donation->currency,
                    'status' => $donation->status,
                    'created_at' => optional($donation->created_at)->toDateTimeString(),
                    'elder' => $donation->elder ? [
                        'name' => $donation->elder->name,
                        'branch_id' => $donation->elder->branch_id,
                    ] : null,
                ]),
        ];

        $filename = sprintf(
            'backups/%s-encrypted.json.enc',
            now()->format('Ymd_His')
        );

        $encrypted = Crypt::encryptString(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $disk->put($filename, $encrypted);

        $this->purgeOldBackups($disk);

        $this->info("Encrypted backup saved to {$filename} on disk {$diskName}.");

        return Command::SUCCESS;
    }

    private function purgeOldBackups(Filesystem $disk): void
    {
        $retentionDays = config('backup.retention_days', 90);
        $threshold = now()->subDays($retentionDays)->timestamp;

        foreach ($disk->files('backups') as $file) {
            try {
                $modified = $disk->lastModified($file);
            } catch (FileNotFoundException) {
                continue;
            }

            if ($modified < $threshold) {
                $disk->delete($file);
            }
        }
    }
}
