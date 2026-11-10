<?php

// format numbers
function format($n)
{
    $n = (float) $n;

    return number_format($n, 2, '.', '');
}

function formatc($n)
{
    $n = (float) $n;

    return number_format($n, 2, ',', '');
}

function format_bytes($bytes, $decimals = 0)
{
    $symbols = ['B', 'KB', 'MB', 'GB', 'TB'];

    $exp = floor(log($bytes) / log(1024));

    $formatedBytes = round($bytes / pow(1024, $exp), $decimals).' '.$symbols[$exp];

    return $formatedBytes;
}

function strip_letters($string)
{
    $number = preg_replace('/[^0-9]/', '', $string);
    $number = (float) $number;

    return $number;
}

function numberToWord($number, $lang = 'it')
{

    $number = round($number);

    $word = '';

    // thousands
    if ($number >= 1000) {

        $thousands = floor($number / 1000);
        $number -= $thousands * 1000;

        $word .= $thousands == 1 ? 'mille' : digitToWord($thousands).'mila';
    }

    // hundreds
    if ($number >= 100) {

        $hundreds = floor($number / 100);
        $number -= $hundreds * 100;

        $word .= $hundreds == 1 ? 'cento' : digitToWord($hundreds).'cento';
    }

    // tens
    if ($number >= 20) {

        $tens = floor($number / 10);
        $number = $number - $tens * 10;

        switch ($tens) {
            case 2:
                $tensWord = 'venti';
                break;
            case 3:
                $tensWord = 'trenta';
                break;
            case 4:
                $tensWord = 'quaranta';
                break;
            case 5:
                $tensWord = 'cinquanta';
                break;
            case 6:
                $tensWord = 'sessanta';
                break;
            case 7:
                $tensWord = 'settanta';
                break;
            case 8:
                $tensWord = 'ottanta';
                break;
            case 9:
                $tensWord = 'novanta';
                break;
        }

        if (in_array($number, [1, 8])) {
            $tensWord = substr($tensWord, 0, -1);
        }

        $word .= $tensWord;
    }

    // numbers under 20
    if ($number) {
        $word .= digitToWord($number);
    }

    return $word;
}

// numbers till 19 to word conversion
function digitToWord($digit)
{

    switch ($digit) {
        case 1:
            $word = 'uno';
            break;
        case 2:
            $word = 'due';
            break;
        case 3:
            $word = 'tre';
            break;
        case 4:
            $word = 'quattro';
            break;
        case 5:
            $word = 'cinque';
            break;
        case 6:
            $word = 'sei';
            break;
        case 7:
            $word = 'sette';
            break;
        case 8:
            $word = 'otto';
            break;
        case 9:
            $word = 'nove';
            break;
        case 10:
            $word = 'dieci';
            break;
        case 11:
            $word = 'undici';
            break;
        case 12:
            $word = 'dodici';
            break;
        case 13:
            $word = 'tredici';
            break;
        case 14:
            $word = 'quattordici';
            break;
        case 15:
            $word = 'quindici';
            break;
        case 16:
            $word = 'sedici';
            break;
        case 17:
            $word = 'diciassette';
            break;
        case 18:
            $word = 'diciotto';
            break;
        case 19:
            $word = 'diciannove';
            break;
    }

    return $word;
}

function random_string($length = 10)
{

    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';

    $charactersLength = strlen($characters);

    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}
