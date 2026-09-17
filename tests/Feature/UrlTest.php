<?php

test('that authentication URLs return a 200', function (string $url) {
    $this->get($url)->assertOk();
})->with('urls');
