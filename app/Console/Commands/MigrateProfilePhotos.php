<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateProfilePhotos extends Command
{
    protected $signature = 'profile-photos:migrate {--dry-run : Show what would be moved without changing files or database}';

    protected $description = 'Move legacy profile photos into storage/app/public/profile_pictures and update users.photo_path.';

    public function handle(): int
    {
        $users = User::query()
            ->whereNotNull('photo_path')
            ->orderBy('id')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No profile photos found to migrate.');
            return self::SUCCESS;
        }

        $migrated = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $currentPath = trim((string) $user->photo_path);

            if ($currentPath === '') {
                $skipped++;
                continue;
            }

            if (Str::startsWith($currentPath, 'profile_pictures/')) {
                $skipped++;
                continue;
            }

            $sourceCandidates = [
                storage_path('app/public/' . $currentPath),
                public_path($currentPath),
            ];

            $sourcePath = null;

            foreach ($sourceCandidates as $candidate) {
                if (File::exists($candidate)) {
                    $sourcePath = $candidate;
                    break;
                }
            }

            if ($sourcePath === null) {
                $this->warn("User {$user->id} ({$user->email}): source file not found for {$currentPath}");
                $skipped++;
                continue;
            }

            $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
            $baseName = pathinfo($sourcePath, PATHINFO_FILENAME);
            $safeBaseName = Str::slug($baseName) ?: 'profile-photo-' . $user->id;
            $newFileName = $safeBaseName . '-' . $user->id;

            if ($extension !== '') {
                $newFileName .= '.' . $extension;
            }

            $relativeTargetPath = 'profile_pictures/' . $newFileName;
            $targetPath = storage_path('app/public/' . $relativeTargetPath);

            while (File::exists($targetPath) && realpath($targetPath) !== realpath($sourcePath)) {
                $newFileName = $safeBaseName . '-' . $user->id . '-' . Str::random(6);
                if ($extension !== '') {
                    $newFileName .= '.' . $extension;
                }

                $relativeTargetPath = 'profile_pictures/' . $newFileName;
                $targetPath = storage_path('app/public/' . $relativeTargetPath);
            }

            $this->line("User {$user->id} ({$user->email}): {$currentPath} -> {$relativeTargetPath}");

            if ($this->option('dry-run')) {
                $migrated++;
                continue;
            }

            File::ensureDirectoryExists(dirname($targetPath));
            File::move($sourcePath, $targetPath);
            $user->update(['photo_path' => $relativeTargetPath]);
            $migrated++;
        }

        $this->info("Profile photo migration complete. Migrated: {$migrated}, skipped: {$skipped}.");

        return self::SUCCESS;
    }
}
