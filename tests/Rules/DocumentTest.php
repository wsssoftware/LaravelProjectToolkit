<?php

use LaravelToolkit\Rules\Document;

it('can\'t pass due not string value', function () {
    $data = ['cpf1' => 02121,];
    $rules = ['cpf1' => Document::cpf(),];
    $result = Validator::make($data, $rules)->errors();
    expect(Arr::get($result->get('cpf1'), 0))
        ->toEqual('The cpf1 field must be a string.');
});

it('can validate a CPF', function () {
    $data = [
        'cpf1' => '927.758.550-16',
        'cpf2' => '927.758.550-20',
        'cpf3' => '927.758.5-20',
    ];
    $rules = [
        'cpf1' => Document::cpf(),
        'cpf2' => Document::cpf(),
        'cpf3' => Document::cpf(),
    ];
    $result = Validator::make($data, $rules)->errors();
    expect($result->has('cpf1'))
        ->toBeFalse()
        ->and(Arr::get($result->get('cpf2'), 0))
        ->toEqual('O campo cpf2 não é um CPF válido.')
        ->and(Arr::get($result->get('cpf3'), 0))
        ->toEqual('O campo cpf3 deve ter 11 dígitos para ser um CPF válido.');
});

it('can validate a CNPJ', function () {
    $data = [
        'cnpj1' => '45.010.043/0001-96',
        'cnpj2' => '45.010.043/0001-93',
        'cnpj3' => '45.010.043/01-96',
    ];
    $rules = [
        'cnpj1' => Document::cnpj(),
        'cnpj2' => Document::cnpj(),
        'cnpj3' => Document::cnpj(),
    ];
    $result = Validator::make($data, $rules)->errors();
    expect($result->has('cnpj1'))
        ->toBeFalse()
        ->and(Arr::get($result->get('cnpj2'), 0))
        ->toEqual('O campo cnpj2 não é um CNPJ válido.')
        ->and(Arr::get($result->get('cnpj3'), 0))
        ->toEqual('O campo cnpj3 deve ter 14 dígitos para ser um CNPJ válido.');
});

it('can validate a Generic', function () {
    $data = [
        'cpf1' => '927.758.550-16',
        'cpf2' => '927.758.550-20',
        'cpf3' => '927.758.5-20',
        'cnpj1' => '45.010.043/0001-96',
        'cnpj2' => '45.010.043/0001-93',
        'cnpj3' => '45.010.043/01-96',
    ];
    $rules = [
        'cpf1' => Document::both(),
        'cpf2' => Document::both(),
        'cpf3' => Document::both(),
        'cnpj1' => Document::both(),
        'cnpj2' => Document::both(),
        'cnpj3' => Document::both(),
    ];
    $result = Validator::make($data, $rules)->errors();
    expect($result->has('cpf1'))
        ->toBeFalse()
        ->and(Arr::get($result->get('cpf2'), 0))
        ->toEqual('O campo cpf2 não é um CNPJ ou CPF válido.')
        ->and(Arr::get($result->get('cpf3'), 0))
        ->toEqual('O campo cpf3 deve ter 11 dígitos para CPF ou 14 dígitos para CNPJ.')
        ->and($result->has('cnpj1'))
        ->toBeFalse()
        ->and(Arr::get($result->get('cnpj2'), 0))
        ->toEqual('O campo cnpj2 não é um CNPJ ou CPF válido.')
        ->and(Arr::get($result->get('cnpj3'), 0))
        ->toEqual('O campo cnpj3 deve ter 11 dígitos para CPF ou 14 dígitos para CNPJ.');
});
