<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Payload;

use fab2s\OverFlow\Interface\NodeInterface;

/**
 * Interface PayloadNodeInterface
 *
 * A Payload Node is supposed to be immutable, and thus
 * have no setters on $isAReturningVal and $isATraversable
 */
interface PayloadNodeInterface extends NodeInterface
{
    /**
     * Get this Node's Payload
     */
    public function getPayload(): callable;
}
