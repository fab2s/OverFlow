<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Flow\Map;

use Exception;
use fab2s\OverFlow\Flow;
use fab2s\OverFlow\Flow\Registry\FlowRegistry;
use fab2s\OverFlow\Flow\Registry\FlowRegistryInterface;
use fab2s\OverFlow\FlowException;
use fab2s\OverFlow\Interface\NodeInterface;
use fab2s\OverFlow\Node\Branch\BranchNodeInterface;

/**
 * class FlowMap
 * Do not implement Serializable interface on purpose
 *
 * @SEE https://externals.io/message/98834#98834
 */
class FlowMap implements FlowMapInterface
{
    /**
     * Flow map
     */
    protected array $nodeMap = [];

    /**
     * @var NodeInterface[]
     */
    protected array $reverseMap = [];

    /**
     * The default Node Map values
     */
    protected array $nodeMapDefault = [
        'class'           => null,
        'flowId'          => null,
        'hash'            => null,
        'index'           => null,
        'isATraversable'  => null,
        'isAReturningVal' => null,
        'isAFlow'         => null,
    ];

    /**
     * The default Node stats values
     */
    protected array $nodeIncrements = [
        'num_exec'     => 0,
        'num_iterate'  => 0,
        'num_break'    => 0,
        'num_continue' => 0,
    ];

    /**
     * The Flow map default values
     */
    protected array $flowMapDefault = [
        'class'    => null,
        'id'       => null,
        'start'    => null,
        'end'      => null,
        'elapsed'  => null,
        'duration' => null,
        'mib'      => null,
    ];
    protected array $incrementTotals = [];
    protected array $flowIncrements;
    protected array $flowStats;
    protected FlowRegistryInterface $registry;
    protected array $registryData = [];
    protected Flow $flow;
    protected string $flowId;

    /**
     * Instantiate a Flow Status
     *
     *
     * @throws FlowException
     * @throws Exception
     */
    public function __construct(Flow $flow, array $flowIncrements = [])
    {
        $this->flow     = $flow;
        $this->flowId   = $this->flow->getId();
        $this->registry = (new FlowRegistry)->registerFlow($flow);
        $this->initDefaults()->setRefs()->setFlowIncrement($flowIncrements);
    }

    /**
     * If you don't feel like doing this at home, I completely
     * understand, I'd be very happy to hear about a better and
     * more efficient way
     *
     * @throws FlowException
     */
    public function __wakeup()
    {
        $this->registry->load($this->flow, $this->registryData);
        $this->setRefs();
    }

    /**
     * @return $this
     *
     * @throws FlowException
     */
    public function register(NodeInterface $node, bool $replace = false): static
    {
        $index = $this->getNodeIndex($node->getId());
        if (! $replace) {
            $this->registry->registerNode($node);
        } else {
            $this->registry->removeNode($this->reverseMap[$this->getNodeIndex($node->getId())]);
        }

        $nodeId                 = $node->getId();
        $this->nodeMap[$nodeId] = array_replace($this->nodeMapDefault, [
            'class'           => get_class($node),
            'flowId'          => $this->flowId,
            'hash'            => $nodeId,
            'index'           => $index,
            'isATraversable'  => $node->isTraversable(),
            'isAReturningVal' => $node->isReturningVal(),
            'isAFlow'         => $node->isFlow(),
        ], $this->nodeIncrements);

        $this->setNodeIncrement($node);

        if (isset($this->reverseMap[$index])) {
            // replacing a node, maintain nodeMap accordingly
            unset($this->nodeMap[$this->reverseMap[$index]->getId()], $this->reverseMap[$index]);
        }

        $this->reverseMap[$index] = $node;

        return $this;
    }

    public function getNodeIndex(string $nodeId): ?int
    {
        return $this->nodeMap[$nodeId]['index'] ?? null;
    }

    /**
     * Triggered right before the flow starts
     *
     * @return $this
     */
    public function flowStart(): static
    {
        $this->flowStats['start'] = microtime(true);

        return $this;
    }

    /**
     * Triggered right after the flow stops
     *
     * @return $this
     */
    public function flowEnd(): static
    {
        $this->flowStats['end']     = microtime(true);
        $this->flowStats['mib']     = memory_get_peak_usage(true) / 1048576;
        $this->flowStats['elapsed'] = $this->flowStats['end'] - $this->flowStats['start'];

        $this->flowStats = array_replace($this->flowStats, $this->duration($this->flowStats['elapsed']));

        return $this;
    }

    /**
     * Let's be fast at incrementing while we are at it
     */
    public function &getNodeStat(string $nodeId): array
    {
        return $this->nodeMap[$nodeId];
    }

    /**
     * Get/Generate Node Map
     */
    public function getNodeMap(): array
    {
        foreach ($this->flow->getNodes() as $node) {
            $nodeId = $node->getId();
            if ($node instanceof BranchNodeInterface) {
                $this->nodeMap[$nodeId]['nodes'] = $node->getFlow()->getNodeMap();
            }
        }

        return $this->nodeMap;
    }

    /**
     * Get the latest Node stats
     *
     * @return array<string,int|string>
     *
     * @throws Exception
     */
    public function getStats(): array
    {
        $this->resetTotals();
        foreach ($this->flow->getNodes() as $node) {
            $nodeMap = $this->nodeMap[$node->getId()];
            foreach ($this->incrementTotals as $srcKey => $totalKey) {
                if (isset($nodeMap[$srcKey])) {
                    $this->flowStats[$totalKey] += $nodeMap[$srcKey];
                }
            }

            if ($node instanceof BranchNodeInterface) {
                $childFlowId                               = $node->getFlow()->getId();
                $this->flowStats['branches'][$childFlowId] = $node->getFlow()->getStats();
                foreach ($this->incrementTotals as $srcKey => $totalKey) {
                    if (isset($this->flowStats['branches'][$childFlowId][$totalKey])) {
                        $this->flowStats[$totalKey] += $this->flowStats['branches'][$childFlowId][$totalKey];
                    }
                }
            }
        }

        $flowStatus = $this->flow->getFlowStatus();
        if ($flowStatus !== null) {
            $this->flowStats['flow_status'] = $flowStatus->getStatus();
        }

        return $this->flowStats;
    }

    /**
     * @return $this
     */
    public function incrementNode(string $nodeId, string $key): static
    {
        $this->nodeMap[$nodeId][$key]++;

        return $this;
    }

    /**
     * @return $this
     */
    public function incrementFlow(string $key): static
    {
        $this->flowStats[$key]++;

        return $this;
    }

    /**
     * Resets Nodes stats, can be used prior to Flow's re-exec
     *
     * @return $this
     */
    public function resetNodeStats(): static
    {
        foreach ($this->nodeMap as &$nodeStat) {
            foreach ($this->nodeIncrements as $key => $value) {
                if (isset($nodeStat[$key])) {
                    $nodeStat[$key] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Computes a human-readable duration string from floating seconds
     *
     *
     * @return array<string,int|string>
     */
    public function duration(float $seconds): array
    {
        $result = [
            'hour' => (int) floor($seconds / 3600),
            'min'  => (int) floor(fmod($seconds / 60, 60)),
            'sec'  => (int) fmod($seconds, 60),
            'ms'   => (int) round(fmod($seconds, 1) * 1000),
        ];

        $duration = '';
        foreach ($result as $unit => $value) {
            if (! empty($value) || $unit === 'ms') {
                $duration .= $value . "$unit ";
            }
        }

        $result['duration'] = trim($duration);

        return $result;
    }

    /**
     * @return $this
     */
    protected function setRefs(): static
    {
        $this->registryData              = &$this->registry->get($this->flowId);
        $this->registryData['flowStats'] = &$this->flowStats;
        $this->registryData['nodeStats'] = &$this->nodeMap;
        $this->registryData['nodes']     = &$this->reverseMap;

        return $this;
    }

    /**
     * @return $this
     */
    protected function initDefaults(): static
    {
        $this->flowIncrements = $this->nodeIncrements;
        foreach (array_keys($this->flowIncrements) as $key) {
            $totalKey                        = $key . '_total';
            $this->incrementTotals[$key]     = $totalKey;
            $this->flowIncrements[$totalKey] = 0;
        }

        $this->flowMapDefault = array_replace($this->flowMapDefault, $this->flowIncrements, [
            'class' => get_class($this->flow),
            'id'    => $this->flowId,
        ]);

        $this->flowStats = $this->flowMapDefault;

        return $this;
    }

    /**
     * @return $this
     */
    protected function resetTotals(): static
    {
        foreach ($this->incrementTotals as $totalKey) {
            $this->flowStats[$totalKey] = 0;
        }

        return $this;
    }

    /**
     * Set additional increment keys, use :
     *      'keyName' => int
     * to add keyName as increment, starting at int
     * or :
     *      'keyName' => 'existingIncrement'
     * to assign keyName as a reference to existingIncrement
     *
     *
     * @return $this
     *
     * @throws FlowException
     */
    protected function setFlowIncrement(array $flowIncrements): static
    {
        foreach ($flowIncrements as $incrementKey => $target) {
            if (is_string($target)) {
                if (! isset($this->flowStats[$target])) {
                    throw new FlowException('Cannot set reference on unset target');
                }

                $this->flowStats[$incrementKey]            = &$this->flowStats[$target];
                $this->flowStats[$incrementKey . '_total'] = &$this->flowStats[$target . '_total'];

                continue;
            }

            $this->flowIncrements[$incrementKey]  = $target;
            $this->incrementTotals[$incrementKey] = $incrementKey . '_total';
            $this->flowStats[$incrementKey]       = $target;
        }

        return $this;
    }

    /**
     * @return $this
     *
     * @throws FlowException
     */
    protected function setNodeIncrement(NodeInterface $node): static
    {
        $nodeId = $node->getId();
        foreach ($node->getNodeIncrements() as $incrementKey => $target) {
            if (is_string($target)) {
                if (! isset($this->nodeIncrements[$target])) {
                    throw new FlowException('Tried to set an increment alias to an un-registered increment', 1, null, [
                        'aliasKey'  => $incrementKey,
                        'targetKey' => $target,
                    ]);
                }

                $this->nodeMap[$nodeId][$incrementKey] = &$this->nodeMap[$nodeId][$target];

                continue;
            }

            $this->nodeIncrements[$incrementKey]   = $target;
            $this->nodeMap[$nodeId][$incrementKey] = $target;
        }

        return $this;
    }
}
