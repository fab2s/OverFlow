<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Concerns;

use fab2s\OverFlow\Flow;

trait HasCarrier
{
    protected ?Flow $carrier = null;

    public function getCarrier(): ?Flow
    {
        return $this->carrier;
    }

    public function setCarrier(?Flow $carrier): static
    {
        $this->carrier = $carrier;

        return $this;
    }

    public function hasCarrier(): bool
    {
        return (bool) $this->carrier;
    }
}
