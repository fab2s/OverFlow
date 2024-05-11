<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Flow;

use Exception;
use fab2s\OverFlow\Flow\Map\FlowMapInterface;
use fab2s\OverFlow\Flow\Registry\FlowRegistryInterface;
use fab2s\OverFlow\Flow\Status\FlowStatus;
use fab2s\OverFlow\Flow\Status\FlowStatusInterface;
use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Interface\NodeInterface;
use fab2s\OverFlow\Interrupter\InterrupterInterface;
use fab2s\OverFlow\Interrupter\Interruption;

/**
 * Abstract Class FlowInterruptAbstract
 */
abstract class FlowInterruptAbstract extends FlowEventAbstract
{
    /**
     * Continue flag
     */
    protected bool $continue = false;

    /**
     * Break Flag
     */
    protected bool $break = false;
    protected string|bool $interruptNodeId;
    protected FlowMapInterface $flowMap;
    protected FlowRegistryInterface $registry;

    /**
     * Current Flow Status
     */
    protected FlowStatusInterface $flowStatus;

    /**
     * Break the flow's execution, conceptually similar to breaking
     * a regular loop
     *
     *
     * @return $this
     *
     * @throws FlowException
     */
    public function breakFlow(?InterrupterInterface $flowInterrupt = null): static
    {
        return $this->interruptFlow(Interruption::BREAK, $flowInterrupt);
    }

    /**
     * Continue the flow's execution, conceptually similar to continuing
     * a regular loop
     *
     *
     * @return $this
     *
     * @throws FlowException
     */
    public function continueFlow(?InterrupterInterface $flowInterrupt = null): static
    {
        return $this->interruptFlow(Interruption::CONTINUE, $flowInterrupt);
    }

    /**
     * @return $this
     *
     * @throws FlowException
     */
    public function interruptFlow(Interruption $interruptType, ?InterrupterInterface $flowInterrupt = null): static
    {
        $node = $this->nodes[$this->nodeIdx] ?? null;
        if ($interruptType === Interruption::CONTINUE) {
            $this->continue = true;
            $this->flowMap->incrementFlow('num_continue');
            $this->triggerEvent(static::FLOW_CONTINUE, $node);
        } else {
            $this->flowStatus = new FlowStatus(FlowStatus::FLOW_DIRTY);
            $this->break      = true;
            $this->flowMap->incrementFlow('num_break');
            $this->triggerEvent(static::FLOW_BREAK, $node);
        }

        $flowInterrupt?->setType($interruptType)->propagate($this);

        return $this;
    }

    /**
     * Used to set the eventual Node Target of an Interrupt signal
     * set to :
     * - A Node Id to target
     * - true to interrupt every upstream nodes
     *     in this Flow
     * - false to only interrupt up to the first
     *     upstream Traversable in this Flow
     *
     *
     * @return $this
     *
     * @throws FlowException
     * @throws Exception
     */
    public function setInterruptNodeId(null|string|bool $interruptNodeId): static
    {
        if ($interruptNodeId !== null && ! is_bool($interruptNodeId) && ! $this->registry->getNode($interruptNodeId)) {
            throw new FlowException('Targeted Node not found in target Flow for Interruption', 1, null, [
                'targetFlow' => $this->getId(),
                'targetNode' => $interruptNodeId,
            ]);
        }

        $this->interruptNodeId = $interruptNodeId;

        return $this;
    }

    protected function interruptNode(NodeInterface $node): bool
    {
        // if we have an interruptNodeId, bubble up until we match a node
        // else stop propagation
        return $this->interruptNodeId && $this->interruptNodeId !== $node->getId();
    }
}
