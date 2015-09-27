<?php

namespace Phalette\Pidic;

use Nette\DI\Container;

class PiDiContainer extends Container
{

    /**
     * @return array
     */
    public function getServices()
    {
        if (isset($this->meta[self::SERVICES])) {
            return $this->meta[self::SERVICES];
        } else {
            return [];
        }
    }
}
