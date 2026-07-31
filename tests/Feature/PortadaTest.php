<?php

test('la portada responde', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
