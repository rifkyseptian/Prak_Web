<?php
declare(strict_types=1);

namespace FlipPlay;

final class Library
{
    /** @var array<string, Media> */
    private array $media = [];

    public function addMedia(Media $media): bool
    {
        if (isset($this->media[$media->getId()])) {
            return false;
        }

        $this->media[$media->getId()] = $media;
        return true;
    }

    /** @return array<string, Media> */
    public function getAllMedia(): array
    {
        return $this->media;
    }

    public function findById(string $id): ?Media
    {
        return $this->media[$id] ?? null;
    }

    /** @return list<Media> */
    public function searchByTitle(string $query): array
    {
        return array_values(array_filter(
            $this->media,
            static fn (Media $media): bool =>
                str_contains(
                    mb_strtolower($media->getTitle()),
                    mb_strtolower($query)
                )
        ));
    }
}