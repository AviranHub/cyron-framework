<?php

namespace Cyron\Queue;

interface Job
{
    public function handle();
}
