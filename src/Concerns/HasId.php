<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Concerns;

use Exception;
use fab2s\SoUuid\SoUuid;

trait HasId
{
    protected ?string $id = null;

    /**
     * @throws Exception
     */
    public function getId(): string
    {
        if ($this->id === null) {
            return $this->id = SoUuid::generate()->getBase36();
        }

        return $this->id;
    }

    protected function resetId(): static
    {
        $this->id = null;

        return $this;
    }
}
