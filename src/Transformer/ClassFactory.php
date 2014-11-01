<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer\Transformer;

/**
 * ClassFactory is a contract for creating one class type
 */
interface ClassFactory
{

    public function create(array $exported);

    public function getFqcn();
}
