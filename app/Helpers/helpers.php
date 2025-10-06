<?php


use Carbon\Carbon;

/**
 * Mengonversi total jam menjadi format hari, jam, dan menit yang mudah dibaca.
 *
 * @param float $totalHours Total durasi dalam jam.
 * @return string String yang sudah diformat (contoh: "4 hari 4 jam").
 */
function formatDuration(int $totalMinutes): string
{
    if ($totalMinutes <= 0) {
        return 'Kurang dari 1 menit';
    }

    // 1 hari = 1440 menit (60 * 24)
    $minutesInDay = 1440;

    // Hitung jumlah hari
    $days = floor($totalMinutes / $minutesInDay);

    // Hitung sisa menit setelah dikurangi hari
    $remainingMinutes = $totalMinutes % $minutesInDay;

    // Hitung jam dari sisa menit
    $hours = floor($remainingMinutes / 60);

    // Hitung sisa menit terakhir
    $minutes = $remainingMinutes % 60;

    $parts = [];

    if ($days > 0) {
        $parts[] = $days . ' hari';
    }
    if ($hours > 0) {
        $parts[] = $hours . ' jam';
    }
    if ($minutes > 0) {
        $parts[] = $minutes . ' menit';
    }

    if (empty($parts)) {
        return 'Kurang dari 1 menit';
    }

    return implode(' ', $parts);
}