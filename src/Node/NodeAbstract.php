<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node;

use fab2s\OverFlow\Concerns\HasCarrier;
use fab2s\OverFlow\Concerns\HasId;
use fab2s\OverFlow\Flow;
use fab2s\OverFlow\Flow\Registry\FlowRegistry;
use fab2s\OverFlow\Flow\Registry\FlowRegistryInterface;
use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Interface\NodeInterface;

/**
 * abstract Class NodeAbstract
 */
abstract class NodeAbstract implements NodeInterface
{
    use HasCarrier;
    use HasId;

    /**
     * Indicate if this Node is traversable
     */
    protected bool $isATraversable;

    /**
     * Indicate if this Node is returning a value
     */
    protected bool $isAReturningVal;

    /**
     * Indicate if this Node is a Flow (Branch)
     */
    protected bool $isAFlow;
    protected FlowRegistryInterface $registry;
    protected array $nodeIncrements = [];

    /**
     * @throws FlowException
     */
    public function __construct()
    {
        $this->enforceIsATraversable();
        $this->registry = new FlowRegistry;
    }

    public function isTraversable(): bool
    {
        return $this->isATraversable;
    }

    /**
     * Indicate if this Node is a Flow (Branch)
     *
     * @return bool true if this node instanceof NodalFlow
     */
    public function isFlow(): bool
    {
        return $this->isAFlow;
    }

    /**
     * @return bool true if this node is expected to return
     *              something to pass on next node as param.
     *              If nothing is returned, the previously
     *              returned value will be used as param
     *              for next nodes.
     */
    public function isReturningVal(): bool
    {
        return $this->isAReturningVal;
    }

    /**
     * Get the custom Node increments to be considered during
     * Flow execution
     * To set additional increment keys, use :
     *      'keyName' => int
     * to add keyName as increment, starting at int
     * or :
     *      'keyName' => 'existingIncrement'
     * to assign keyName as a reference to an existingIncrement
     */
    public function getNodeIncrements(): array
    {
        return $this->nodeIncrements;
    }

    /**
     * @throws FlowException
     */
    public function sendTo(string $flowId, ?string $nodeId = null, mixed $param = null): mixed
    {
        if (! ($flow = $this->registry->getFlow($flowId))) {
            throw new FlowException('Cannot sendTo without valid Flow target', 1, null, [
                'flowId' => $flowId,
                'nodeId' => $nodeId,
            ]);
        }

        return $flow->sendTo($nodeId, $param);
    }

    /**
     * @throws FlowException
     */
    protected function enforceIsATraversable(): static
    {
        if ($this->isFlow()) {
            if ($this->isATraversable) {
                throw new FlowException('Cannot Traverse a Branch');
            }

            return $this;
        }

        if ($this->isATraversable) {
            if (! ($this instanceof TraversableNodeInterface)) {
                throw new FlowException('Cannot Traverse a Node that does not implement TraversableNodeInterface');
            }

            return $this;
        }

        if (! ($this instanceof ExecNodeInterface)) {
            throw new FlowException('Cannot Exec a Node that does not implement ExecNodeInterface');
        }

        return $this;
    }

    public function handle(mixed $input = null): iterable
    {
        if ($this->isTraversable()) {
            /** @var TraversableNodeInterface $this */
            return $this->getTraversable($input);
        }

        /** @var ExecNodeInterface $this */
        yield $this->exec($input);
    }

    public function __clone(): void
    {
        $this->resetId()->setCarrier(null);
    }
}
