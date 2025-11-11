<?php

if (! function_exists('rateify_stars')) {
    function rateify_stars($item)
    {
        return view('rateify::widget', compact('item'))->render();
    }
}

if (! function_exists('rateify_average')) {
    function rateify_average($item)
    {
        return $item->averageRating();
    }
}
