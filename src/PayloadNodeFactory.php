<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow;

use Closure;
use fab2s\OverFlow\Node\Branch\BranchNode;
use fab2s\OverFlow\Node\Payload\CallableNode;
use fab2s\OverFlow\Node\Payload\ClosureNode;
use fab2s\OverFlow\Node\Payload\PayloadNodeFactoryInterface;

/**
 * class PayloadNodeFactory
 */
class PayloadNodeFactory implements PayloadNodeFactoryInterface
{
    /**
     * Instantiate the proper Payload Node for the payload
     *
     *
     * @throws FlowException
     */
    public static function create($payload, bool $isAReturningVal, bool $isATraversable = false): CallableNode|ClosureNode|BranchNode
    {
        if (\is_array($payload) || \is_string($payload)) {
            return new CallableNode($payload, $isAReturningVal, $isATraversable);
        }

        if ($payload instanceof Closure) {
            // distinguishing Closures actually is surrealistic
            return new ClosureNode($payload, $isAReturningVal, $isATraversable);
        }

        if ($payload instanceof Flow) {
            return new BranchNode($payload, $isAReturningVal);
        }

        throw new FlowException('Payload not supported, must be Callable or Flow');
    }
}
