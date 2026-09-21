<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/i18n.json');

        if (! is_file($path)) {
            $this->command?->warn('i18n.json missing — skipping translation import.');

            return;
        }

        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $now = now();
        $rows = [];

        foreach ($data['exact'] as $source => $values) {
            $rows[] = $this->row($source, $values, 'site', false, $now);
        }

        foreach ($data['lowercase'] as $source => $values) {
            $rows[] = $this->row($source, $values, 'lowercase', false, $now);
        }

        foreach ($data['patterns'] as $pattern) {
            $rows[] = $this->row($pattern['source'], $pattern['values'], 'pattern', true, $now);
        }

        // A source can appear in more than one group. Keep the richest entry:
        // 'site' outranks 'lowercase', which only exists for pattern fill-ins.
        $rank = ['site' => 2, 'pattern' => 2, 'lowercase' => 1];

        $rows = collect($rows)
            ->sortBy(fn (array $row) => $rank[$row['group']] ?? 0)
            ->keyBy('source_hash')
            ->values()
            ->all();

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('translations')->upsert(
                $chunk,
                ['source_hash'],
                ['source', 'group', 'values', 'is_pattern', 'updated_at'],
            );
        }

        $this->command?->info('Imported '.count($rows).' translation entries.');
    }

    protected function row(string $source, array $values, string $group, bool $isPattern, $now): array
    {
        return [
            'group' => $group,
            'source' => $source,
            'source_hash' => Translation::hashFor($source),
            'values' => json_encode($values, JSON_UNESCAPED_UNICODE),
            'is_pattern' => $isPattern,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
