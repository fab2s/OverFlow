<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Payload;

use fab2s\OverFlow\Node\Branch\BranchNode;

/**
 * Interface PayloadNodeFactoryInterface
 */
interface PayloadNodeFactoryInterface
{
    /**
     * Instantiate the proper Payload Node for the payload
     */
    public static function create(callable $payload, bool $isAReturningVal, bool $isATraversable = false): CallableNode|ClosureNode|BranchNode;
}
