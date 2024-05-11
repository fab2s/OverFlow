<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Flow;

use fab2s\OverFlow\Concerns\HasId;
use fab2s\OverFlow\Flow;
use fab2s\OverFlow\Interface\IdInterface;
use fab2s\OverFlow\Interface\NodeInterface;
use fab2s\OverFlow\Node\TraversableNodeInterface;

/**
 * Abstract Class FlowAncestryAbstract
 */
abstract class FlowAncestryAbstract implements IdInterface
{
    use HasId;

    /**
     * The underlying node structure
     *
     * @var TraversableNodeInterface|NodeInterface[]
     */
    protected array $nodes = [];

    /**
     * The current Node index, being the next slot in $this->nodes for
     * node additions and the current node index when executing the flow
     */
    protected int $nodeIdx = 0;

    /**
     * The last index value
     */
    protected int $lastIdx = 0;

    /**
     * The parent Flow, only set when branched
     */
    protected ?Flow $parent;

    /**
     * Set parent Flow, happens only when branched
     *
     *
     * @return $this
     */
    public function setParent(Flow $flow): static
    {
        $this->parent = $flow;

        return $this;
    }

    /**
     * Get eventual parent Flow
     */
    public function getParent(): static
    {
        return $this->parent;
    }

    /**
     * Tells if this flow has a parent
     */
    public function hasParent(): bool
    {
        return isset($this->parent);
    }

    /**
     * Get this Flow's root Flow
     *
     * @param Flow $flow Root Flow, or self if root flow
     */
    public function getRootFlow(Flow $flow): static
    {
        while ($flow->hasParent()) {
            $flow = $flow->getParent();
        }

        return $flow;
    }
}
