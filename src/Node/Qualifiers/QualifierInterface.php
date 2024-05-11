<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Node\Qualifiers;

use fab2s\OverFlow\Interrupter\InterrupterInterface;
use fab2s\OverFlow\Node\ExecNodeInterface;

/**
 * Interface QualifierInterface
 */
interface QualifierInterface extends ExecNodeInterface
{
    /**
     * Qualifies a record to either keep it, skip it or break the flow at the execution point
     * or at any upstream Node
     *
     *
     * @return InterrupterInterface|bool|null|void `true` to accept the record, eg let the Flow proceed untouched
     *                                             `false|null|void` to deny the record, eg trigger a continue on the carrier Flow (not ancestors)
     *                                             `InterrupterInterface` to trigger an interrupt with a target (which may be one ancestor)
     */
    public function qualify(mixed $param): InterrupterInterface|bool|null;
}
