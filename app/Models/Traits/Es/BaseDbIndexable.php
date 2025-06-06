<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Models\Traits\Es;

use App\Libraries\Elasticsearch\Es;
use Log;

trait BaseDbIndexable
{
    use BaseIndexable;

    abstract public static function esIndexingQuery();

    public static function esIndexIntoNew($batchSize = 1000, $name = null, ?callable $progress = null)
    {
        $newIndex = $name ?? static::esTimestampedIndexName();
        Log::info("Creating new index {$newIndex}");
        static::esCreateIndex($newIndex);

        $options = [
            'index' => $newIndex,
        ];

        static::esReindexAll($batchSize, 0, $options, $progress);

        return $newIndex;
    }

    public static function esIndexName()
    {
        return $GLOBALS['cfg']['osu']['elasticsearch']['prefix'].(new static())->getTable();
    }

    public static function esSchemaFile()
    {
        return config_path('schemas/'.(new static())->getTable().'.json');
    }

    public static function esReindexAll($batchSize = 1000, $fromId = 0, array $options = [], ?callable $progress = null)
    {
        $dummy = new static();
        $startTime = time();

        $baseQuery = static::esIndexingQuery()->where($dummy->getKeyName(), '>', $fromId);
        $count = 0;
        $progress ??= fn ($count, $message) => \Log::info(static::class.' '.$message);

        $baseQuery->chunkById($batchSize, function ($models) use ($options, &$count, $progress) {
            $actions = Es::generateBulkActions($models);

            if ($actions !== []) {
                $result = Es::getClient()->bulk([
                    'index' => $options['index'] ?? static::esIndexName(),
                    'body' => $actions,
                    'client' => ['timeout' => 0],
                ]);

                $count += count($result['items']);
            }

            $progress($count, "next: {$models->last()->getKey()}");
        });

        $duration = time() - $startTime;

        $progress($count, $message = "Indexed {$count} records in {$duration} s.");
    }

    /**
     * The value for routing.
     * Override to provide a routing value; null by default.
     *
     * @return string|null
     */
    public function esRouting()
    {
        // null will be omitted when used as routing.
    }

    public function esDeleteDocument(array $options = [])
    {
        $document = [
            'index' => static::esIndexName(),
            'routing' => $this->esRouting(),
            'id' => $this->getEsId(),
            'client' => ['ignore' => 404],
            ...$options,
        ];

        return Es::getClient()->delete($document);
    }

    public function esIndexDocument(array $options = [])
    {
        if (!$this->esShouldIndex()) {
            return $this->esDeleteDocument($options);
        }

        $document = [
            'index' => static::esIndexName(),
            'routing' => $this->esRouting(),
            'id' => $this->getEsId(),
            'body' => $this->toEsJson(),
            ...$options,
        ];

        return Es::getClient()->index($document);
    }

    public function esShouldIndex()
    {
        return true;
    }

    public function getEsId()
    {
        return $this->getKey();
    }

    public function toEsJson(): array
    {
        $mappings = static::esMappings();

        $document = [];
        foreach ($mappings as $field => $mapping) {
            $value = $this->getEsFieldValue($field);

            $document[$field] = $value instanceof \DateTimeInterface
                ? json_time($value)
                : $value;
        }

        return $document;
    }

    abstract protected function getEsFieldValue(string $field);
}
