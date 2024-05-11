<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Branch;

use fab2s\OverFlow\Concerns\HasFlow;
use fab2s\OverFlow\Flow;
use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Node\NodeAbstract;

/**
 * Class BranchNode
 */
class BranchNode extends NodeAbstract implements BranchNodeInterface
{
    use HasFlow;
    protected bool $isAFlow = true;

    /**
     * Instantiate the BranchNode
     *
     *
     * @throws FlowException
     */
    public function __construct(protected Flow $flow, bool $isAReturningVal)
    {
        // branch Node does not (yet) support traversing
        $this->isATraversable  = false;
        $this->isAReturningVal = $isAReturningVal;
        parent::__construct();
    }

    /**
     * Execute the BranchNode
     *
     * @throws FlowException
     */
    public function exec(mixed $param = null): mixed
    {
        // in the branch case, we actually exec a Flow
        return $this->flow->exec($param);
    }
}
