<?php

/**
 * Format a price value according to the current currency
 */
function price(?float $value): string
{
    $formatter = new NumberFormatter(config('app.locale'), NumberFormatter::CURRENCY);

    return $formatter->formatCurrency($value, config('custom.currency', 'EUR'));
}
