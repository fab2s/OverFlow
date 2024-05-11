<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Interrupter;

use Exception;
use fab2s\OverFlow\Concerns\HasFlowTarget;
use fab2s\OverFlow\Flow;
use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Interface\NodeInterface;
use InvalidArgumentException;

/**
 * Class Interrupter
 */
class Interrupter implements InterrupterInterface
{
    use HasFlowTarget;
    protected ?string $nodeTarget;

    /**
     * Interrupter constructor.
     *
     * @param Flow|Target|string $flowTarget , target up to Targeted Flow id or InterrupterInterface::TARGET_TOP
     *                                       to interrupt every parent
     *
     * @throws Exception
     */
    public function __construct(Flow|Target|string $flowTarget, NodeInterface|string|null $nodeTarget = null, protected Interruption $type)
    {
        $this->setFlowTarget($flowTarget);

        $this->nodeTarget = $nodeTarget instanceof NodeInterface ? $nodeTarget->getId() : $nodeTarget;
    }

    public function getType(): Interruption
    {
        return $this->type;
    }

    /**
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setType(Interruption $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Trigger the Interrupt of each ancestor Flows up to a specific one, the root one
     * or none if :
     * - No FlowInterrupt is set
     * - FlowInterrupt is set at InterrupterInterface::TARGET_SELF
     * - FlowInterrupt is set at this Flow's Id
     * - FlowInterrupt is set as InterrupterInterface::TARGET_TOP and this has no parent
     *
     * Throw an exception if we reach the top after bubbling and FlowInterrupt != InterrupterInterface::TARGET_TOP
     *
     *
     * @throws FlowException
     */
    public function propagate(Flow $flow): Flow
    {
        // evacuate edge cases
        if ($this->isEdgeInterruptCase($flow)) {
            // if anything had to be done, it was done first hand already
            // just make sure we propagate the eventual nodeTarget
            return $flow->setInterruptNodeId($this->nodeTarget);
        }

        $InterrupterFlowId = $flow->getId();

        do {
            $lastFlowId = $flow->getId();
            if ($this->flowTarget === $lastFlowId) {
                // interrupting $flow
                return $flow->setInterruptNodeId($this->nodeTarget)->interruptFlow($this->type);
            }

            // Set interruptNodeId to true in order to make sure
            // we do not match any nodes in this flow (as it is not the target)
            $flow->setInterruptNodeId(true)->interruptFlow(Interruption::BREAK);
        } while ($flow->hasParent() && $flow = $flow->getParent());

        if ($this->flowTarget !== Target::TOP) {
            throw new FlowException('Interruption target missed', 1, null, [
                'interruptAt'       => $this->flowTarget,
                'InterrupterFlowId' => $InterrupterFlowId,
                'lastFlowId'        => $lastFlowId,
            ]);
        }

        return $flow;
    }

    public function interruptNode(?NodeInterface $node = null): bool
    {
        return $node && $this->nodeTarget === $node->getId();
    }

    protected function isEdgeInterruptCase(Flow $flow): bool
    {
        return
                // asked to stop right here
                $this->flowTarget    === Target::SELF
                || $this->flowTarget === $flow->getId()
                || (
                    // target root when this Flow is root already
                    $this->flowTarget === Target::TOP
                    && ! $flow->hasParent()
                );
    }
}
