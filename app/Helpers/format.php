<?php

function formatCurrencyShort($amount) {
    if ($amount >= 1000000) return '₱' . number_format($amount / 1000000, 2) . 'M';
    if ($amount >= 1000) return '₱' . number_format($amount / 1000, 2) . 'K';
    return '₱' . number_format($amount, 2);
}