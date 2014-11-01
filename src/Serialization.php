<?php

/*
 * SerialIcer
 */

namespace Trismegiste\SerialIcer;

/**
 * Serialization is a contract for ...
 *
 * @author flo
 */
interface Serialization
{

    const CLASS_KEY = '@class';
    const UUID_KEY = '@uuid';
    const REF_KEY = '@ref';

//    public function isSpecialClass($fqcn);
}
