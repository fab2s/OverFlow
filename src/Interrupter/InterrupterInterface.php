<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Interrupter;

use fab2s\OverFlow\Flow;
use fab2s\OverFlow\FlowException;
use InvalidArgumentException;

/**
 * Interface InterrupterInterface
 */
interface InterrupterInterface
{
    public function getType(): Interruption;

    /**
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setType(Interruption $type): static;

    /**
     * Trigger the Interrupt of each ancestor Flows up to a specific one, the root one
     * or none if :
     * - No FlowInterrupt is set
     * - FlowInterrupt is set at this Flow's Id
     * - FlowInterrupt is set as InterrupterInterface::TARGET_TOP and this has no parent
     *
     * Throw an exception if we reach the top after bubbling and FlowInterrupt != InterrupterInterface::TARGET_TOP
     *
     * @throws FlowException
     */
    public function propagate(Flow $flow): Flow;
}
