<?php
declare(strict_types=1);

namespace FlipPlay;

abstract class Media
{
    public function __construct(
        private string $id,
        private string $title,
        private string $creator,
        private int $releaseYear
    ) {
        if ($id === '' || $title === '' || $creator === '') {
            throw new \InvalidArgumentException(
                'ID, judul, dan kreator wajib diisi.'
            );
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCreator(): string
    {
        return $this->creator;
    }

    public function getReleaseYear(): int
    {
        return $this->releaseYear;
    }

    abstract public function getType(): string;

    public function getDetails(): string
    {
        return sprintf(
            '[%s] %s — %s (%d)',
            $this->id,
            $this->title,
            $this->creator,
            $this->releaseYear
        );
    }
}

final class Movie extends Media
{
    public function __construct(
        string $id,
        string $title,
        string $creator,
        int $releaseYear,
        private int $durationMinutes
    ) {
        parent::__construct($id, $title, $creator, $releaseYear);
    }

    public function getType(): string
    {
        return 'Film';
    }

    public function getDetails(): string
    {
        return parent::getDetails()
            . sprintf(' | Durasi: %d menit', $this->durationMinutes);
    }
}

final class Game extends Media
{
    public function __construct(
        string $id,
        string $title,
        string $creator,
        int $releaseYear,
        private string $platform
    ) {
        parent::__construct($id, $title, $creator, $releaseYear);
    }

    public function getType(): string
    {
        return 'Game';
    }

    public function getDetails(): string
    {
        return parent::getDetails()
            . ' | Platform: ' . $this->platform;
    }
}