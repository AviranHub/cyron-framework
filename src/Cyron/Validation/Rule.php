<?php

namespace Cyron\Validation;

interface Rule
{
    public function passes($field, $value, $data);
    public function message($field);
}
