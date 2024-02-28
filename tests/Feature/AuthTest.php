<?php

it('has auth page', function () {
    $response = $this->get('/auth');

    $response->assertStatus(200);
});
