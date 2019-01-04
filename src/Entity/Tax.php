<?php

declare(strict_types=1);

namespace Recruitment\Entity;

class Tax
{
    #TODO perhaps each tax value as separate const for clearer code
    const ALLOWED_VALUES = [0, 5, 8, 23];

    private $value;

    public function __construct(int $value)
    {
        if (!in_array($value, self::ALLOWED_VALUES)) {
            throw new \InvalidArgumentException();
        };

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
