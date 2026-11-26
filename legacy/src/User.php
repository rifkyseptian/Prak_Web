<?php
declare(strict_types=1);

namespace FlipPlay;

final class User
{
    /** @var array<string, Media> */
    private array $collection = [];

    public function __construct(private string $name)
    {
        if (trim($name) === '') {
            throw new \InvalidArgumentException(
                'Nama pengguna wajib diisi.'
            );
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addToCollection(Media $media): bool
    {
        if (isset($this->collection[$media->getId()])) {
            return false;
        }

        $this->collection[$media->getId()] = $media;
        return true;
    }

    /** @return array<string, Media> */
    public function getCollection(): array
    {
        return $this->collection;
    }
}