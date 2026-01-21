<?php

namespace App\Console\Commands;

use App\Models\Elder;
use App\Models\HeroSlide;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class NormalizeMediaAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:normalize {--dry-run : Preview the number of records affected without updating them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace placeholder/remote media references with local Mekodonia assets.';

    private const ELDER_FALLBACK = 'images/monk-mekodoniya.jpg';
    private const HERO_FALLBACK = '/images/hero-father.svg';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info($dryRun ? 'Running media normalization in dry-run mode…' : 'Normalizing media paths…');

        $elderChanges = $this->normalizeElders($dryRun);
        $heroChanges = $this->normalizeHeroSlides($dryRun);

        $this->line(sprintf(
            'Elders updated: %d | Hero slides updated: %d%s',
            $elderChanges,
            $heroChanges,
            $dryRun ? ' (dry-run)' : ''
        ));

        return self::SUCCESS;
    }

    private function normalizeElders(bool $dryRun): int
    {
        $query = Elder::withoutGlobalScopes()->where(function ($builder) {
            $builder->whereNull('profile_picture_path')
                ->orWhere('profile_picture_path', '')
                ->orWhere('profile_picture_path', 'like', 'http%')
                ->orWhere('profile_picture_path', 'like', 'https%')
                ->orWhere('profile_picture_path', 'like', 'storage/http%')
                ->orWhere('profile_picture_path', 'like', 'storage/https%')
                ->orWhere('profile_picture_path', 'like', '%placeholder.com%');
        });

        if ($dryRun) {
            return $query->count();
        }

        return $query->update(['profile_picture_path' => self::ELDER_FALLBACK]);
    }

    private function normalizeHeroSlides(bool $dryRun): int
    {
        $query = HeroSlide::query()->where(function ($builder) {
            $builder->whereNull('image')
                ->orWhere('image', '')
                ->orWhere('image', 'like', 'http%')
                ->orWhere('image', 'like', 'https%')
                ->orWhere('image', 'like', '%placeholder.com%');
        });

        if ($dryRun) {
            return $query->count();
        }

        return $query->update(['image' => self::HERO_FALLBACK]);
    }
}
