<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Services\Entities;


use Descent\Services\Exceptions\ServiceException;

/**
 * Class Factory
 * @package Descent\Services\Entities
 */
class Factory extends AbstractService
{
    /**
     * Dispatches the provided concrete.
     *
     * @param $concrete
     * @return string|callable
     */
    protected function dispatchConcrete($concrete)
    {
        if ( ! is_callable($concrete) ) {
            throw new ServiceException(
                'A factory concrete must be a callable'
            );
        }

        return $concrete;
    }

}