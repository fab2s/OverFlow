<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Flow\Status;

use fab2s\OverFlow\FlowException;
use Throwable;

/**
 * class FlowStatus
 */
class FlowStatus implements FlowStatusInterface
{
    /**
     * Flow statuses
     */
    const FLOW_RUNNING   = 'running';
    const FLOW_CLEAN     = 'clean';
    const FLOW_DIRTY     = 'dirty';
    const FLOW_EXCEPTION = 'exception';

    /**
     * Flow status
     */
    protected string $status;
    protected Throwable $exception;

    /**
     * Flow statuses
     */
    protected array $flowStatuses = [
        self::FLOW_RUNNING   => self::FLOW_RUNNING,
        self::FLOW_CLEAN     => self::FLOW_CLEAN,
        self::FLOW_DIRTY     => self::FLOW_DIRTY,
        self::FLOW_EXCEPTION => self::FLOW_EXCEPTION,
    ];

    /**
     * Instantiate a Flow Status
     *
     * @param string $status The flow status
     *
     * @throws FlowException
     */
    public function __construct(string $status, ?Throwable $e = null)
    {
        if (! isset($this->flowStatuses[$status])) {
            throw new FlowException('$status must be one of :' . \implode(', ', $this->flowStatuses));
        }

        $this->status    = $status;
        $this->exception = $e;
    }

    /**
     * Get a string representation of the Flow status
     *
     * @return string The flow status
     */
    public function __toString(): string
    {
        return $this->getStatus();
    }

    /**
     * Indicate that the flow is currently running
     * useful for branched flow to find out what is
     * their parent up to and distinguish between top
     * parent end and branch end
     *
     * @return bool True If the flow is currently running
     */
    public function isRunning(): bool
    {
        return $this->status === static::FLOW_RUNNING;
    }

    /**
     * Tells if the Flow went smoothly
     *
     * @return bool True If everything went well during the flow
     */
    public function isClean(): bool
    {
        return $this->status === static::FLOW_CLEAN;
    }

    /**
     * Indicate that the flow was interrupted by a Node
     * s
     *
     * @return bool True If the flow was interrupted without exception
     */
    public function isDirty(): bool
    {
        return $this->status === static::FLOW_DIRTY;
    }

    /**
     * Indicate that an exception was raised during the Flow execution
     *
     * @return bool True If the flow was interrupted with exception
     */
    public function isException(): bool
    {
        return $this->status === static::FLOW_EXCEPTION;
    }

    /**
     * Return the Flow status
     *
     * @return string The flow status
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Return the eventual exception throw during the flow execution
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }
}
