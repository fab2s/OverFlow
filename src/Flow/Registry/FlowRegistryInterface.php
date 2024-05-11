<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Flow\Registry;

use fab2s\OverFlow\Flow;
use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Interface\NodeInterface;

/**
 * Interface FlowRegistryInterface
 */
interface FlowRegistryInterface
{
    /**
     * @throws FlowException
     */
    public function registerFlow(Flow $flow): static;

    /**
     * @throws FlowException
     */
    public function registerNode(NodeInterface $node): static;

    public function getFlow(string $flowId): ?Flow;

    public function getNode(string $nodeID): ?NodeInterface;

    public function removeNode(NodeInterface|string $node): static;
}
