<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Services\Container;


use Descent\Services\Entities\ServiceInterface;

/**
 * Interface DependencyBuilderInterface
 * @package Descent\Services\Container
 */
interface DependencyBuilderInterface
{
    /**
     * incubates the instance for a provided interface. Optional $parameters content supersedes assigned or incubated
     * parameters. Optionally enforces the provided optional parameter names.
     *
     * @param string $interface
     * @param array $parameters
     * @param \string[] ...$enforcedOptionalParameters
     * @return mixed
     */
    public function make(string $interface, array $parameters = [], string ... $enforcedOptionalParameters);

    /**
     * calls the provided callback. Optional $parameters content supersedes incubated parameters. Optionally enforces
     * the provided optional parameter names.
     *
     * @param callable $callback
     * @param array $parameters
     * @param \string[] ...$enforcedOptionalParameters
     * @return mixed
     */
    public function call(callable $callback, array $parameters = [], string ... $enforcedOptionalParameters);

    /**
     * incubates the provided service. Optional $parameters content supersedes assigned or incubated parameters.
     * Optionally enforces the provided optional parameter names.
     *
     * @param ServiceInterface $service
     * @param array $parameters
     * @param \string[] ...$enforcedOptionalParameters
     * @return object
     */
    public function build(ServiceInterface $service, array $parameters = [], string ... $enforcedOptionalParameters);
}