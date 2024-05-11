<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Interrupt;

use fab2s\OverFlow\Interrupter\InterrupterInterface;
use fab2s\OverFlow\Interrupter\Interruption;
use fab2s\OverFlow\Node\NodeAbstract;

/**
 * Abstract Class InterruptNodeAbstract
 */
abstract class InterruptNodeAbstract extends NodeAbstract implements InterruptNodeInterface
{
    /**
     * Indicate if this Node is traversable
     */
    protected bool $isATraversable = false;

    /**
     * Indicate if this Node is returning a value
     */
    protected bool $isAReturningVal = false;

    /**
     * Indicate if this Node is a Flow (Branch)
     */
    protected bool $isAFlow         = false;
    protected array $nodeIncrements = [
        'num_interrupt' => 'num_exec',
    ];

    /**
     * The interrupt's method interface is simple :
     *      - return false to break
     *      - return true to continue
     *      - return void|null (whatever) to proceed with the flow
     */
    public function exec(mixed $param = null): mixed
    {
        $flowInterrupt = $this->interrupt($param);
        if ($flowInterrupt === null) {
            // do nothing, let the flow proceed
            return;
        }

        if ($flowInterrupt instanceof InterrupterInterface) {
            $flowInterruptType = $flowInterrupt->getType();
        } elseif ($flowInterrupt) {
            $flowInterruptType = Interruption::CONTINUE;
            $flowInterrupt     = null;
        } else {
            $flowInterruptType = Interruption::BREAK;
            $flowInterrupt     = null;
        }

        /* @var null|InterrupterInterface $flowInterrupt */
        $this->carrier->interruptFlow($flowInterruptType, $flowInterrupt);
    }
}
