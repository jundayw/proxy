<?php

if (!function_exists('array_some')) {
    /**
     * Checks if at least one element in an array satisfies a given condition.
     *
     * This function applies a callback to each element of the array. If the callback
     * returns `true` for any element, `array_some` returns `true`. If the callback
     * returns `false` for all elements, it returns `false`.
     *
     * Note: This implementation uses `array_reduce`, which always iterates through the
     * entire array without early termination. For large arrays, consider using a
     * `foreach` loop if short‑circuiting is desired.
     *
     * @param array    $array        The array to check.
     * @param callable $callback     The callback function. It should accept one argument:
     *                               the value of the current element. It must return a
     *                               boolean value.<br>
     *                               Signature: `fn(mixed $value): bool`
     * @param int      $mode         Flag determining what arguments are sent to callback:
     *                               <b>ARRAY_FILTER_USE_KEY</b> - pass key as the only argument to callback instead of the value
     *                               <b>ARRAY_FILTER_USE_BOTH</b> - pass both value and key as arguments to callback instead of the value
     *
     * @return bool `true` if the callback returns `true` for at least one element, `false` otherwise.
     */
    function array_some(array $array, callable $callback, int $mode = 0): bool
    {
        return array_reduce(array_map(fn($k, $v) => match ($mode) {
            ARRAY_FILTER_USE_BOTH => [$v, $k],
            ARRAY_FILTER_USE_KEY => [$k],
            default => [$v]
        }, array_keys($array), array_values($array)), static fn($carry, $item) => $carry || call_user_func_array($callback, $item), false);
    }
}
