<?php

namespace App\Services;

class StatusFlow
{
    public const FLOW = [
        'Pending/Ongoing', 'Successful Bidding', 'Contract Signed',
        'Obligated', 'Installed/Renewed', 'Delivered', 'Submitted DV',
    ];

    public static function allowed(?string $current, bool $hasContract, bool $hasDocs): array
    {
        if (in_array($current, ['Cancelled', 'Submitted DV'], true)) {
            return [$current];
        }

        if ($current === 'Failed Bidding') {
            return ['Failed Bidding', 'Pending/Ongoing', 'Cancelled'];
        }

        $i    = (int) array_search($current, self::FLOW, true);
        $next = self::FLOW[$i + 1] ?? null;

        $open = match ($next) {
            'Contract Signed' => $hasContract,
            'Obligated'       => $hasDocs,
            default           => true,
        };

        return array_values(array_filter([
            self::FLOW[$i],
            $open ? $next : null,
            $i === 0 ? 'Failed Bidding' : null,
            'Cancelled',
        ]));
    }

    public static function reached(?string $current, string $step): bool
    {
        $i = array_search($current, self::FLOW, true);

        return $i !== false && $i >= array_search($step, self::FLOW, true);
    }

    // stage 3
    public static function allowedSchedule(?string $current, bool $hasDocs, bool $scheduled): array
    {
        if (in_array($current, ['Cancelled', 'Submitted DV'], true)) {
            return [$current];
        }

        $i   = (int) array_search($current, self::FLOW, true);
        $out = [$current];

        foreach (array_slice(self::FLOW, $i + 1) as $step) {
            $ok = match ($step) {
                'Obligated'                    => $hasDocs,
                'Installed/Renewed', 'Delivered' => $scheduled,
                default                        => $step === self::FLOW[$i + 1],
            };

            if ($ok) {
                $out[] = $step;
            }
        }

        $out[] = 'Cancelled';

        return $out;
    }
}