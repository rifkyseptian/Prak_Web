<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Media.php';
require_once __DIR__ . '/src/User.php';
require_once __DIR__ . '/src/Library.php';

use FlipPlay\Game;
use FlipPlay\Library;
use FlipPlay\Media;
use FlipPlay\Movie;
use FlipPlay\User;

function ask(string $prompt): string
{
    echo $prompt;
    return trim((string) fgets(STDIN));
}

/**
 * @param list<Media> $items
 */
function showMedia(array $items): void
{
    if ($items === []) {
        echo "Belum ada media yang cocok.\n";
        return;
    }

    foreach ($items as $media) {
        echo '• ' . $media->getType()
            . ' — ' . $media->getDetails()
            . "\n";
    }
}

$library = new Library();
$user = new User('Pengguna Demo');

$library->addMedia(
    new Movie('M001', 'Laskar Pelangi', 'Riri Riza', 2008, 124)
);
$library->addMedia(
    new Game('G001', 'Stardew Valley', 'ConcernedApe', 2016, 'PC / Switch')
);
$library->addMedia(
    new Movie('M002', 'Keluarga Cemara', 'Yandy Laurens', 2019, 110)
);

echo "=== FlipPlay: Simulasi Library Media ===\n";
echo 'Halo, ' . $user->getName() . "!\n";

while (true) {
    echo "\n1. Lihat semua media"
        . "\n2. Cari judul"
        . "\n3. Lihat detail media"
        . "\n4. Tambah film/game"
        . "\n5. Tambahkan media ke koleksi saya"
        . "\n6. Lihat koleksi saya"
        . "\n0. Keluar\n";

    $choice = ask('Pilih menu: ');

    if ($choice === '0') {
        echo "Sampai jumpa di FlipPlay!\n";
        break;
    }

    switch ($choice) {
        case '1':
            showMedia(array_values($library->getAllMedia()));
            break;

        case '2':
            $query = ask('Masukkan kata pada judul: ');
            showMedia($library->searchByTitle($query));
            break;

        case '3':
            $media = $library->findById(ask('Masukkan ID media: '));

            if ($media !== null) {
                echo $media->getDetails() . "\n";
            } else {
                echo "Media tidak ditemukan.\n";
            }
            break;

        case '4':
            $type = strtolower(ask('Jenis (film/game): '));
            $id = strtoupper(ask('ID unik: '));
            $title = ask('Judul: ');
            $creator = ask('Sutradara/pengembang: ');
            $year = filter_var(
                ask('Tahun rilis: '),
                FILTER_VALIDATE_INT
            );

            if ($year === false || !in_array($type, ['film', 'game'], true)) {
                echo "Jenis atau tahun tidak valid.\n";
                break;
            }

            if ($type === 'film') {
                $duration = (int) (ask('Durasi (menit): ') ?: '0');
                $media = new Movie(
                    $id,
                    $title,
                    $creator,
                    $year,
                    $duration
                );
            } else {
                $platform = ask('Platform: ');
                $media = new Game(
                    $id,
                    $title,
                    $creator,
                    $year,
                    $platform
                );
            }

            if ($library->addMedia($media)) {
                echo "Media berhasil ditambahkan.\n";
            } else {
                echo "ID sudah digunakan.\n";
            }
            break;

        case '5':
            $media = $library->findById(ask('Masukkan ID media: '));

            if ($media === null) {
                echo "Media tidak ditemukan.\n";
            } elseif ($user->addToCollection($media)) {
                echo "Media masuk ke koleksi.\n";
            } else {
                echo "Media sudah ada di koleksi.\n";
            }
            break;

        case '6':
            echo 'Koleksi milik ' . $user->getName() . ":\n";
            showMedia(array_values($user->getCollection()));
            break;

        default:
            echo "Pilihan tidak tersedia.\n";
    }
}