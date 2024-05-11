<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node;

use fab2s\OverFlow\Interface\NodeInterface;

/**
 * Interface ExecNodeInterface
 */
interface ExecNodeInterface extends NodeInterface
{
    /**
     * Execute this Node
     *
     *
     * @return mixed The result of this node
     *               execution with this param
     */
    public function exec(mixed $param = null): mixed;
}
