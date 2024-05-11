<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Payload;

use Closure;
use fab2s\OverFlow\FlowException;
use Generator;

/**
 * Class ClosureNode
 */
class ClosureNode extends CallableNode
{
    /**
     * Instantiate a Closure Node
     *
     *
     * @throws FlowException
     */
    public function __construct(Closure $payload, bool $isAReturningVal, bool $isATraversable = false)
    {
        parent::__construct($payload, $isAReturningVal, $isATraversable);
    }

    /**
     * Get this Node's Traversable (payload must be consistent for the usage)
     *
     *
     * @return Generator
     */
    public function getTraversable($param = null): iterable
    {
        $callable = $this->payload;
        foreach ($callable($param) as $value) {
            yield $value;
        }
    }

    /**
     * Execute this Node (payload must be consistent for the usage)
     *
     * @param mixed|null $param
     */
    public function exec($param = null)
    {
        $callable = $this->payload;

        return $callable($param);
    }
}
