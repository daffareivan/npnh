<?php

test('the application returns a successful response', function () {
    $response = $this->get('/signin');

    $response->assertStatus(200);
});
