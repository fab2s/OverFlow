<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Concerns;

use fab2s\OverFlow\Flow;
use fab2s\OverFlow\Interrupter\Target;

trait HasFlowTarget
{
    protected Target|string $flowTarget;

    public function setFlowTarget(Flow|Target|string $flowTarget): static
    {
        $this->flowTarget = $flowTarget instanceof Flow ? $flowTarget->getId() : (Target::tryFrom($flowTarget) ?: $flowTarget);

        return $this;
    }

    public function getFlowTarget(): Target|string
    {
        return $this->flowTarget;
    }
}
