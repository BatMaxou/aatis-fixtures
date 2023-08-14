<?php

namespace Aatis\FixturesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AatisFixturesBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
