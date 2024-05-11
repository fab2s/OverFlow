<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Event;

use fab2s\OverFlow\Flow;
use fab2s\OverFlow\Interface\NodeInterface;

/**
 * Interface FlowEventInterface
 */
interface FlowEventInterface
{
    public function getFlow(): Flow;

    public function getNode(): ?NodeInterface;

    /**
     * @return $this
     */
    public function setNode(?NodeInterface $node = null): static;
}
