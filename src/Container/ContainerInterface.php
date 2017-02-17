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
use Descent\Services\Exceptions\ServiceContainerExceptionInterface;

/**
 * Interface ContainerInterface
 * @package Descent\Services\Container
 */
interface ContainerInterface
{
    /**
     * concrete ServiceInterface instance getter.
     *
     * @param string $interface
     * @throws ServiceContainerExceptionInterface
     * @return ServiceInterface
     */
    public function get($interface): ServiceInterface;

    /**
     * checks whether the given interfaces are known to the container or not.
     *
     * @param string $interface
     * @param string[] ...$interfaces
     * @return bool
     */
    public function has($interface, string ... $interfaces);

    /**
     * binds a given interface to a optionally provided concrete. If no concrete is provided, the provided interface
     * will be bound to itself.
     *
     * @param string $interface
     * @param string|object|null $concrete
     * @throws ServiceContainerExceptionInterface when the provided concrete is not a string, object (not Closure) or null.
     * @return ServiceInterface
     */
    public function bind(string $interface, $concrete = null): ServiceInterface;

    /**
     * binds a given interface to a given callback as a factory. The callback must define the interface as its return
     * type.
     *
     * @param string $interface
     * @param callable $callback
     * @throws ServiceContainerExceptionInterface when the provided callback does not have the provided interface as its return type.
     * @return ServiceInterface
     */
    public function factory(string $interface, callable $callback): ServiceInterface;

    /**
     * clones the instance. Optional provided interfaces define the protected service list of this container.
     * When the provided interfaces are omitted, the entire containment will be assigned as protected services.
     *
     * @param \string[] ...$interfaces
     * @return ContainerInterface
     */
    public function split(string ... $interfaces): ContainerInterface;

    /**
     * clones the instance. Optional provided interfaces are filtered from new container instance. When no interface
     * is provided, the new container will have the entire containment of the expelled container assigned as
     * protected services.
     *
     * @param \string[] ...$interfaces
     * @return ContainerInterface
     */
    public function expel(string ... $interfaces): ContainerInterface;
}