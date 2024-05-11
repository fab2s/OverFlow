<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Qualifiers;

use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Interrupter\InterrupterInterface;
use fab2s\OverFlow\Interrupter\Interruption;
use fab2s\OverFlow\Node\NodeAbstract;

/**
 * Abstract Class QualifierAbstract
 */
abstract class QualifierAbstract extends NodeAbstract implements QualifierInterface
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
        'num_qualify' => 'num_exec',
    ];

    /**
     * The qualify's method interface is simple :
     *      - return true to qualify the record, that is to use it
     *      - return false|null|void to skip the record
     *      - return InterrupterInterface to leverage complete interruption features
     *
     *
     *
     * @throws FlowException
     */
    public function exec(mixed $param = null): InterrupterInterface|bool|null
    {
        $qualifies = $this->qualify($param);
        if ($qualifies === true) {
            return null;
        }

        if (empty($qualifies)) {
            $this->carrier?->interruptFlow(Interruption::CONTINUE);

            return null;
        }

        if ($qualifies instanceof InterrupterInterface) {
            $this->carrier?->interruptFlow($qualifies->getType(), $qualifies);

            return null;
        }

        throw new FlowException('Qualifier returned wrong type, only Boolean, nullish and InterrupterInterface are allowed');
    }
}
