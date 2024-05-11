<?php

/*
 * This file is part of fab2s/OverFlow.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/OverFlow
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OverFlow\Interface;

/**
 * Interface FlowIdInterface
 */
interface IdInterface
{
    /**
     * Return the immutable unique Flow / Node id
     *
     * @return string Immutable unique id
     */
    public function getId(): string;
}
