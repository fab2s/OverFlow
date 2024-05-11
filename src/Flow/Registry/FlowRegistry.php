<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Flow\Registry;

use Exception;
use fab2s\OverFlow\Concerns\HasCarrier;
use fab2s\OverFlow\Flow;
use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Interface\NodeInterface;

/**
 * class FlowRegistry
 */
class FlowRegistry implements FlowRegistryInterface
{
    use HasCarrier;
    protected static array $registry = [];

    /**
     * @var array<int, Flow>
     */
    protected array $flows = [];

    /**
     * @var array<int, NodeInterface>
     */
    protected array $nodes = [];

    /**
     * @var array<string, int>
     */
    protected array $reverseMap = [];

    public static function make(): static
    {
        return new static;
    }

    /**
     * @return $this
     *
     * @throws FlowException
     * @throws Exception
     */
    public function registerFlow(Flow $flow): static
    {
        $flowId = $flow->getId();
        if (isset(static::$registry[$flowId])) {
            throw new FlowException('Duplicate Flow instances are not allowed', 1, null, [
                'flowClass' => $flow::class,
                'flowId'    => $flowId,
            ]);
        }

        static::$registry[$flowId] = $flow;

        $this->flows[$flowId] = $flow;

        return $this;
    }

    /**
     * @return $this
     *
     * @throws FlowException
     */
    public function registerNode(NodeInterface $node): static
    {
        $nodeId = $node->getId();
        if (isset(static::$registry[$nodeId])) {
            throw new FlowException('Duplicate Node instances are not allowed', 1, null, [
                'nodeClass' => $node::class,
                'nodeId'    => $nodeId,
            ]);
        }

        static::$registry[$nodeId] = $node;

        $this->nodes[$nodeId] = $node;

        return $this;
    }

    public function getFlow(string $flowId): ?Flow
    {
        return $this->flows[$flowId] ?? null;
    }

    public function getNode(string $nodeId): ?NodeInterface
    {
        return $this->nodes[$nodeId] ?? null;
    }

    /**
     * @throws Exception
     */
    public static function getFlowRegistry(Flow|string $flow): ?Flow
    {
        $flowId = $flow instanceof Flow ? $flow->getId() : $flow;

        return static::$registry[$flowId] ?? null;
    }

    /**
     * @throws Exception
     */
    public static function getNodeRegistry(NodeInterface|string $node): ?NodeInterface
    {
        $nodeId = $node instanceof Flow ? $node->getId() : $node;

        return static::$registry[$nodeId] ?? null;
    }

    /**
     * @return $this
     */
    public function removeNode(NodeInterface $node): static
    {
        static::$registry[$node->getId()] = null;

        $this->nodes[$node->getId()] = null;

        return $this;
    }

    public function __sleep(): array
    {
        return ['carrier'];
    }

    /**
     * @throws FlowException
     */
    public function __wakeup(): void
    {
        if (! $this->hasCarrier()) {
            return;
        }

        $this->registerFlow($this->getCarrier());
        foreach ($this->getCarrier()->getNodes() as $node) {
            $this->registerNode($node);
        }
    }
}
