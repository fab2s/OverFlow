<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Interface;

use fab2s\OverFlow\Flow;
use fab2s\OverFlow\FlowException;

/**
 * Interface NodeInterface
 */
interface NodeInterface extends IdInterface
{
    /**
     * Indicate if the Node is Traversable
     */
    public function isTraversable(): bool;

    /**
     * Indicate if the Node is a Flow (branch)
     *
     * @return bool true if this node is an instanceof NodalFlow
     */
    public function isFlow(): bool;

    /**
     * Indicate if the Node is returning a value
     *
     * @return bool true if this node is expected to return
     *              something to pass to the next node as param.
     *              If nothing is returned, the previously
     *              returned value will be use as param
     *              for next nodes.
     */
    public function isReturningVal(): bool;

    /**
     * Set/Reset carrying Flow
     *
     *
     * @return $this
     */
    public function setCarrier(?Flow $flow): static;

    /**
     * Return the carrying Flow
     */
    public function getCarrier(): ?Flow;

    /**
     * @throws FlowException
     */
    public function sendTo(string $flowId, ?string $nodeId = null, mixed $param = null): mixed;

    /**
     * Get the custom Node increments to be considered during
     * Flow execution
     * To set additional increment keys, use :
     *      'keyName' => int
     * to add keyName as increment, starting at int
     * or :
     *      'keyName' => 'existingIncrement'
     * to assign keyName as a reference to an existingIncrement
     */
    public function getNodeIncrements(): array;

    public function handle(mixed $input = null): iterable;
}
