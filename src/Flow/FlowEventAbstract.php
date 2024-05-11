<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Flow;

use fab2s\OverFlow\Event\FlowEvent;
use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Interface\NodeInterface;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract Class FlowEventAbstract
 */
abstract class FlowEventAbstract extends FlowAncestryAbstract
{
    public const FLOW_START    = 'flow.start';
    public const FLOW_PROGRESS = 'flow.progress';
    public const FLOW_CONTINUE = 'flow.continue';
    public const FLOW_BREAK    = 'flow.break';
    public const FLOW_SUCCESS  = 'flow.success';
    public const FLOW_FAIL     = 'flow.fail';
    public const EVENTS        = [
        self::FLOW_START    => self::FLOW_START,
        self::FLOW_PROGRESS => self::FLOW_PROGRESS,
        self::FLOW_CONTINUE => self::FLOW_CONTINUE,
        self::FLOW_BREAK    => self::FLOW_BREAK,
        self::FLOW_SUCCESS  => self::FLOW_SUCCESS,
        self::FLOW_FAIL     => self::FLOW_FAIL,
    ];

    /**
     * Progress modulo to apply
     * Set to x if you want to trigger
     * progress every x iterations in flow
     */
    protected int $progressMod                      = 1024;
    protected ?EventDispatcherInterface $dispatcher = null;
    protected array $activeEvents;
    protected array $dispatchArgs   = [];
    protected int $eventInstanceKey = 0;
    protected int $eventNameKey     = 1;

    /**
     * @var array<callable>
     */
    protected array $on = [];

    /**
     * Get current $progressMod
     */
    public function getProgressMod(): int
    {
        return $this->progressMod;
    }

    /**
     * Define the progress modulo, Progress Callback will be
     * triggered upon each iteration in the flow modulo $progressMod
     *
     *
     * @return $this
     */
    public function setProgressMod(int $progressMod): static
    {
        $this->progressMod = max(1, $progressMod);

        return $this;
    }

    /**
     * @throws ReflectionException
     */
    public function getDispatcher(): EventDispatcherInterface
    {
        if ($this->dispatcher === null) {
            $this->dispatcher = new EventDispatcher;
            $this->initDispatchArgs(EventDispatcher::class);
        }

        return $this->dispatcher;
    }

    /**
     * @return $this
     *
     * @throws ReflectionException
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): static
    {
        $this->dispatcher = $dispatcher;

        return $this->initDispatchArgs(get_class($dispatcher));
    }

    /**
     * @return $this
     */
    protected function triggerEvent(string $eventName, ?NodeInterface $node = null): static
    {
        if (isset($this->activeEvents[$eventName])) {
            $this->dispatchArgs[$this->eventNameKey] = $eventName;
            $this->dispatchArgs[$this->eventInstanceKey]->setNode($node);
            $this->dispatcher->dispatch(...$this->dispatchArgs);
        }

        return $this->handleOn($eventName);
    }

    /**
     * @return $this
     */
    protected function listActiveEvent(bool $reload = false): static
    {
        if (! isset($this->dispatcher) || (isset($this->activeEvents) && ! $reload)) {
            return $this;
        }

        $this->activeEvents = [];
        $eventList          = static::getEventList();
        $sortedListeners    = $this->dispatcher->getListeners();
        foreach ($sortedListeners as $eventName => $listeners) {
            if (isset($eventList[$eventName]) && ! empty($listeners)) {
                $this->activeEvents[$eventName] = 1;
            }
        }

        return $this;
    }

    /**
     * @throws ReflectionException
     */
    protected function initDispatchArgs(string $class): static
    {
        $reflection         = new ReflectionMethod($class, 'dispatch');
        $firstParam         = $reflection->getParameters()[0];
        $this->dispatchArgs = [
            new FlowEvent($this),
            null,
        ];

        if ($firstParam->getName() !== 'event') {
            $this->eventInstanceKey = 1;
            $this->eventNameKey     = 0;
            $this->dispatchArgs     = array_reverse($this->dispatchArgs);
        }

        return $this;
    }

    /**
     * @param string<key-of<static::EVENTS>> $when
     *
     * @return $this
     *
     * @throws FlowException
     */
    public function on(string $when, callable $callback): static
    {
        if (! isset(static::getEventList()[$when])) {
            throw new FlowException('Event "' . $when . '" does not exists.');
        }

        $this->on[$when][] = $callback;

        return $this;
    }

    protected function handleOn(string $what): static
    {
        foreach ($this->on[$what] as $callable) {
            call_user_func($callable, $this);
        }

        return $this;
    }

    /**
     * @return array<key-of<static::EVENTS>, value-of<static::EVENTS>>
     */
    public static function getEventList(): array
    {
        return static::EVENTS;
    }
}
