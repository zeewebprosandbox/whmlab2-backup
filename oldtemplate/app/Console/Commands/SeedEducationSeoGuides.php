<?php

namespace App\Console\Commands;

use App\Models\Frontend;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedEducationSeoGuides extends Command
{
    protected $signature = 'zodpanel:seed-education-seo {--force : Overwrite existing education guides}';
    protected $description = 'Seed 60+ deeply technical, SEO and AI-optimized hosting education guides into the database';

    public function handle(): int
    {
        $this->info('Starting generation of 60+ high-authority SEO & AI hosting education guides...');

        $filePath = app_path('Support/guides_seo_dataset.json');
        if (!file_exists($filePath)) {
            $this->error("Dataset file not found at: {$filePath}");
            return 1;
        }

        $guides = json_decode(file_get_contents($filePath), true);
        if (!is_array($guides)) {
            $this->error("Failed to decode JSON dataset.");
            return 1;
        }

        $count = count($guides);
        $this->info("Loaded {$count} comprehensive guides across multiple categories.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $inserted = 0;
        $updated = 0;

        foreach ($guides as $guide) {
            $slug = Str::slug($guide['title']);

            $existing = Frontend::where('data_keys', 'blog.element')
                ->where('slug', $slug)
                ->first();

            $dataValues = [
                'title' => $guide['title'],
                'category' => $guide['category'],
                'reading_time' => $guide['reading_time'],
                'description' => $guide['content'],
                'image' => 'assets/images/frontend/blog/default.png',
                'tags' => implode(', ', $guide['keywords']),
            ];

            $seoContent = [
                'description' => $guide['meta_description'],
                'social_title' => $guide['title'] . ' | ' . gs('site_name') . ' Guides',
                'social_description' => $guide['meta_description'],
                'keywords' => $guide['keywords'],
            ];

            if ($existing) {
                if ($this->option('force')) {
                    $existing->data_values = $dataValues;
                    $existing->seo_content = $seoContent;
                    $existing->save();
                    $updated++;
                }
            } else {
                $frontend = new Frontend();
                $frontend->data_keys = 'blog.element';
                $frontend->slug = $slug;
                $frontend->data_values = $dataValues;
                $frontend->seo_content = $seoContent;
                $frontend->save();
                $inserted++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ Successfully processed guides! (Inserted: {$inserted}, Updated: {$updated}, Total: {$count})");

        return 0;
    }
}
