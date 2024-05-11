<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Payload;

use fab2s\OverFlow\Concerns\HasPayload;
use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Node\NodeAbstract;

/**
 * abstract class PayloadNodeAbstract
 */
abstract class PayloadNodeAbstract extends NodeAbstract implements PayloadNodeInterface
{
    use HasPayload;

    /**
     * @throws FlowException
     */
    public function __construct(callable $payload, protected bool $isAReturningVal, protected bool $isATraversable = false)
    {
        $this->payload = $payload;

        parent::__construct();
    }
}
