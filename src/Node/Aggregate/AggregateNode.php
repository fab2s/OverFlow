<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Aggregate;

use fab2s\OverFlow\Concerns\HasFlow;
use fab2s\OverFlow\Flow;
use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Node\NodeAbstract;
use fab2s\OverFlow\Node\TraversableNodeInterface;
use Generator;

/**
 * class AggregateNode
 */
class AggregateNode extends NodeAbstract implements AggregateNodeInterface
{
    use HasFlow;
    protected bool $isAFlow = false;

    /**
     * Instantiate an Aggregate Node
     *
     *
     * @throws FlowException
     */
    public function __construct(protected bool $isAReturningVal)
    {
        parent::__construct();
        $this->isATraversable = true;
        $this->flow           = Flow::make();
    }

    /**
     * Add a traversable to the aggregate
     *
     *
     * @return $this
     *
     * @throws FlowException
     */
    public function addTraversable(TraversableNodeInterface $node): static
    {
        $this->flow->add($node);

        return $this;
    }

    /**
     * Get the traversable to traverse within the Flow
     *
     *
     * @return Generator
     */
    public function getTraversable(mixed $param = null): iterable
    {
        $value = null;
        /** @var $nodes TraversableNodeInterface[] */
        $nodes = $this->flow->getNodes();
        foreach ($nodes as $node) {
            $returnVal = $node->isReturningVal();
            foreach ($node->getTraversable($param) as $value) {
                if ($returnVal) {
                    yield $value;

                    continue;
                }

                yield $param;
            }

            if ($returnVal) {
                // since this node is returning something
                // we will pass its last yield to the next
                // traversable. It will be up to him to
                // do whatever is necessary with it, including
                // nothing
                $param = $value;
            }
        }
    }

    /**
     * Execute the BranchNode
     *
     *
     * @throws FlowException
     */
    public function exec(mixed $param = null): mixed
    {
        throw new FlowException('AggregateNode cannot be executed, use getTraversable to iterate instead');
    }
}
