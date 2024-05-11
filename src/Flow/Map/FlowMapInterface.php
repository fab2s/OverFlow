<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Flow\Map;

use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Interface\NodeInterface;

/**
 * Interface FlowMapInterface
 */
interface FlowMapInterface
{
    public function getNodeIndex(string $nodeId): ?int;

    /**
     * Triggered right before the flow starts
     *
     * @return $this
     */
    public function flowStart(): static;

    /**
     * Triggered right after the flow stops
     *
     * @return $this
     */
    public function flowEnd(): static;

    /**
     * Let's be fast at incrementing while we are at it
     */
    public function &getNodeStat(string $nodeId): array;

    /**
     * @return $this
     *
     * @throws FlowException
     */
    public function register(NodeInterface $node, bool $replace = false): static;

    /**
     * @return $this
     */
    public function incrementNode(string $nodeId, string $key): static;

    /**
     * @return $this
     */
    public function incrementFlow(string $key): static;

    /**
     * Get/Generate Node Map
     */
    public function getNodeMap(): array;

    /**
     * Get the latest Node stats
     *
     * @return array<string,int|string>
     */
    public function getStats(): array;
}
