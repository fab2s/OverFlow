<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Flow;

use fab2s\OverFlow\Flow\Map\FlowMapInterface;
use fab2s\OverFlow\Flow\Status\FlowStatusInterface;
use fab2s\OverFlow\Interface\NodeInterface;

/**
 * Abstract Class FlowAbstract
 */
abstract class FlowAbstract extends FlowInterruptAbstract
{
    /**
     * Get the stats array with latest Node stats
     *
     * @return array<string,int|string>
     */
    public function getStats(): array
    {
        return $this->flowMap->getStats();
    }

    /**
     * Get the stats array with latest Node stats
     */
    public function getFlowMap(): FlowMapInterface
    {
        return $this->flowMap;
    }

    /**
     * Get the Node array
     *
     * @return NodeInterface[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * Get/Generate Node Map
     */
    public function getNodeMap(): array
    {
        return $this->flowMap->getNodeMap();
    }

    /**
     * The Flow status can either indicate be:
     *      - clean (isClean()): everything went well
     *      - dirty (isDirty()): one Node broke the flow
     *      - exception (isException()): an exception was raised during the flow
     */
    public function getFlowStatus(): ?FlowStatusInterface
    {
        return $this->flowStatus;
    }

    public function __clone(): void
    {
        $this->resetId();
    }
}
