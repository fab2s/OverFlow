<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Qualifiers;

use Exception;
use fab2s\OverFlow\Concerns\HasCount;
use fab2s\OverFlow\Concerns\HasFlowTarget;
use fab2s\OverFlow\Concerns\HasLimit;
use fab2s\OverFlow\Flow;
use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Interrupter\Interrupter;
use fab2s\OverFlow\Interrupter\InterrupterInterface;
use fab2s\OverFlow\Interrupter\Interruption;
use fab2s\OverFlow\Interrupter\Target;

/**
 * Class LimitQualifier
 */
class LimitQualifier extends QualifierAbstract
{
    use HasCount;
    use HasFlowTarget;
    use HasLimit;
    protected int $count = 0;

    /**
     * @throws FlowException
     * @throws Exception
     */
    public function __construct(?int $limit = null, Flow|Target|string $target = Target::SELF)
    {
        parent::__construct();

        $this->setLimit($limit)
            ->setFlowTarget($target)
        ;
    }

    /**
     * @throws Exception
     */
    public function qualify(mixed $param): InterrupterInterface|bool|null
    {
        if ($this->limit && ++$this->count >= $this->limit + 1) {
            return new Interrupter($this->getFlowTarget(), null, Interruption::BREAK);
        }

        return true;
    }
}
