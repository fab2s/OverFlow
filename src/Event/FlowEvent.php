<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Event;

use fab2s\OverFlow\Concerns\HasFlow;
use fab2s\OverFlow\Flow;
use fab2s\OverFlow\Interface\NodeInterface;
use Symfony\Contracts\EventDispatcher\Event;

class FlowEvent extends Event implements FlowEventInterface
{
    use HasFlow;
    protected static array $eventList;
    protected ?NodeInterface $node;

    /**
     * FlowEvent constructor.
     */
    public function __construct(Flow $flow, ?NodeInterface $node = null)
    {
        $this->flow = $flow;
        $this->node = $node;
    }

    public function getNode(): ?NodeInterface
    {
        return $this->node;
    }

    public function setNode(?NodeInterface $node = null): static
    {
        $this->node = $node;

        return $this;
    }
}
