<?php

if (!function_exists('cons')) {

    /**
     * Get constant.
     *
     * @param null|string $key
     *
     * @return \Urland\Constant\Constant|mixed|int
     */
    function cons($key = null)
    {
        $constant = app('constant');
        if (is_null($key)) {
            return $constant;
        }

        return $constant->get($key);
    }
}
