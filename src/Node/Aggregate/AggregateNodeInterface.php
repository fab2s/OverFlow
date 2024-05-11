<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Aggregate;

use fab2s\OverFlow\Node\Branch\BranchNodeInterface;
use fab2s\OverFlow\Node\TraversableNodeInterface;

/**
 * Interface AggregateNodeInterface
 */
interface AggregateNodeInterface extends BranchNodeInterface, TraversableNodeInterface
{
    /**
     * Add a traversable to the aggregate
     *
     * @param TraversableNodeInterface $node A Traversable Node
     *
     * @return $this
     */
    public function addTraversable(TraversableNodeInterface $node): static;
}
