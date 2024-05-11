<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Interrupt;

use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Interrupter\InterrupterInterface;

/**
 * Class CallableInterruptNode
 */
class CallableInterruptNode extends InterruptNodeAbstract
{
    /**
     * @var callable
     */
    protected $interrupter;

    /**
     * Instantiate a CallableInterruptNode Node
     *
     *
     * @throws FlowException
     */
    public function __construct(callable $interrupter)
    {
        $this->interrupter = $interrupter;
        parent::__construct();
    }

    /**
     * @return InterrupterInterface|null|bool `null` do do nothing, eg let the Flow proceed untouched
     *                                        `true` to trigger a continue on the carrier Flow (not ancestors)
     *                                        `false` to trigger a break on the carrier Flow (not ancestors)
     *                                        `InterrupterInterface` to trigger an interrupt to propagate up to a target (which may be one ancestor)
     */
    public function interrupt($param): InterrupterInterface|null|bool
    {
        return \call_user_func($this->interrupter, $param);
    }
}
