<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Payload;

use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Node\ExecNodeInterface;
use fab2s\OverFlow\Node\TraversableNodeInterface;
use Generator;

/**
 * Class CallableNode
 */
class CallableNode extends PayloadNodeAbstract implements ExecNodeInterface, TraversableNodeInterface
{
    /**
     * The underlying executable or traversable Payload
     *
     * @var callable
     */
    protected $payload;

    /**
     * Instantiate a Callable Node
     *
     *
     * @throws FlowException
     */
    public function __construct(callable $payload, bool $isAReturningVal, bool $isATraversable = false)
    {
        parent::__construct($payload, $isAReturningVal, $isATraversable);
    }

    /**
     * Execute this node
     */
    public function exec(mixed $param = null): mixed
    {
        return call_user_func($this->payload, $param);
    }

    /**
     * Get this Node's Traversable
     *
     *
     * @return Generator
     */
    public function getTraversable($param = null): iterable
    {
        foreach (call_user_func($this->payload, $param) as $value) {
            yield $value;
        }
    }
}
