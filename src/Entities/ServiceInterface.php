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

interface ServiceInterface
{
    /**
     * returns the service interface.
     *
     * @return string
     */
    public function getInterface(): string;

    /**
     * returns the concrete.
     *
     * @return string|\Closure
     */
    public function getConcrete();

    /**
     * defines the singleton state of the service.
     *
     * @param bool $flag
     * @return ServiceInterface
     */
    public function singleton(bool $flag = true): ServiceInterface;

    /**
     * checks whether the incubated instance will be served as an instance or not.
     *
     * @return bool
     */
    public function isSingleton(): bool;

    /**
     * binds parameter assignments to the service.
     *
     * @param array $parameters
     * @return ServiceInterface
     */
    public function withParameters(array $parameters): ServiceInterface;

    /**
     * returns the parameter bindings of the service.
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * binds the optional parameters to be resolved.
     *
     * @param \string[] ...$parameters
     * @return ServiceInterface
     */
    public function enforceParameters(string ... $parameters): ServiceInterface;

    /**
     * returns an array of optional parameters being resolved.
     *
     * @return string[]
     */
    public function getEnforcedParameters(): array;

    /**
     * returns the current singleton instance of this object.
     *
     * @throws ServiceException when no instance is stored
     * @throws ServiceException when service does not result into a singleton
     * @return object
     */
    public function getInstance();

    /**
     * checks whether the service holds an instance and the service aims for a singleton.
     *
     * @return bool
     */
    public function hasInstance(): bool;

    /**
     * sets the current instance of the service.
     *
     * @param $object
     * @throws ServiceException when service does not result into a singleton
     * @throws ServiceException when the provided object does not implement the serviced interface
     * @return ServiceInterface
     */
    public function withInstance($object): ServiceInterface;
}