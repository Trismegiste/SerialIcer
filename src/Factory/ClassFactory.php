<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer\Factory;

/**
 * ClassFactory is a contract for creating one class type
 */
interface ClassFactory
{

    public function create(array $exported);

    public function getFqcn();
}
