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
 * Class Service
 * @package Descent\Services\Entities
 */
class Service extends AbstractService
{
    /**
     * Dispatches the provided concrete.
     *
     * @param $concrete
     * @return string|callable
     */
    protected function dispatchConcrete($concrete)
    {
        if ( $concrete instanceof \Closure ) {
            throw new ServiceException(
                'You can not assign Closure instances to a regular service, use a factory instead'
            );
        }

        if ( is_object($concrete) ) {
            $this->singleton();
            $this->withInstance($concrete);
            return get_class($concrete);
        }

        if ( ! is_a($concrete, $this->getInterface(), true) ) {
            throw new ServiceException(
                'The provided concrete does not implement the interface of this service'
            );
        }

        return $concrete;
    }

}