<?php
/*
function normalizeAmount($amountStr, $format = 'us') {
    if ($format === 'us') {
        // Remove commas used as thousands separator
        return floatval(str_replace(',', '', $amountStr));
    } elseif ($format === 'euro') {
        // Replace thousand dot with empty, replace comma with dot
        $clean = str_replace('.', '', $amountStr);
        $clean = str_replace(',', '.', $clean);
        return floatval($clean);
    }
    return 0;
} */


function normalizeAmount($amountStr, $format = 'us') {
    // Trim and remove parentheses (with or without space)
    $amountStr = trim($amountStr);
    $amountStr = preg_replace('/[\(\)]/', '', $amountStr);  // Remove ( and )

    // Optional: remove extra whitespace inside
    $amountStr = trim($amountStr);

    // Remove any characters not digits, comma, or dot
    if (preg_match('/[^0-9.,]/', $amountStr)) {
        return 0; // Invalid string (contains letters or symbols)
    }

    if ($format === 'us') {
        // "13,625,571.00" → 13625571.00
        if (preg_match('/^\d{1,3}(,\d{3})*(\.\d{2})?$/', $amountStr)) {
            return floatval(str_replace(',', '', $amountStr));
        }
    }

    if ($format === 'euro') {
        // "196.734,25" → 196734.25
        if (preg_match('/^\d{1,3}(\.\d{3})*(,\d{2})?$/', $amountStr)) {
            $clean = str_replace('.', '', $amountStr);   // Remove thousand dots
            $clean = str_replace(',', '.', $clean);      // Change decimal comma to dot
            return floatval($clean);
        }
    }

    return 0; // Fallback if format not matched
}


?>