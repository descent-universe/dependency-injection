<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Services;


use Descent\Contracts\ServiceContainerInterface;
use Descent\Contracts\Provider\ServiceProviderInterface;
use Descent\Services\Container\ContainerInterface;
use Descent\Services\Entities\Factory;
use Descent\Services\Entities\ProtectedService;
use Descent\Services\Entities\Service;
use Descent\Services\Entities\ServiceInterface;
use Descent\Services\Exceptions\ServiceContainerException;
use Descent\Services\Exceptions\ServiceContainerExceptionInterface;

class DependencyInjectionContainer extends DependencyBuilder implements ServiceContainerInterface
{
    protected $services = [];

    /**
     * concrete ServiceInterface instance getter.
     *
     * @param string $interface
     * @throws ServiceContainerExceptionInterface
     * @return ServiceInterface
     */
    public function get(string $interface): ServiceInterface
    {
        if ( ! $this->has($interface) ) {
            throw new ServiceContainerException('Unknown interface: '.$interface);
        }

        return $this->services[$this->marshalKey($interface)];
    }

    /**
     * checks whether the given interfaces are known to the container or not.
     *
     * @param \string[] ...$interface
     * @return bool
     */
    public function has(string ... $interface): bool
    {
        foreach ( $interface as $current ) {
            if ( ! array_key_exists($this->marshalKey($interface), $this->services) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * binds a given interface to a optionally provided concrete. If no concrete is provided, the provided interface
     * will be bound to itself.
     *
     * @param string $interface
     * @param string|object|null $concrete
     * @throws ServiceContainerExceptionInterface when the provided concrete is not a string, object (not Closure) or null.
     * @return ServiceInterface
     */
    public function bind(string $interface, $concrete = null): ServiceInterface
    {
        return $this->services[$this->marshalKey($interface)] = new Service($interface, $concrete);
    }

    /**
     * binds a given interface to a given callback as a factory. The callback must define the interface as its return
     * type.
     *
     * @param string $interface
     * @param callable $callback
     * @throws ServiceContainerExceptionInterface when the provided callback does not have the provided interface as its return type.
     * @return ServiceInterface
     */
    public function factory(string $interface, callable $callback): ServiceInterface
    {
        return $this->services[$this->marshalKey($interface)] = new Factory($interface, $callback);
    }

    /**
     * clones the instance. Optional provided interfaces define the protected service list of this container.
     * When the provided interfaces are omitted, the entire containment will be assigned as protected services.
     *
     * @param \string[] ...$interfaces
     * @return ContainerInterface|ServiceContainerInterface
     */
    public function split(string ... $interfaces): ContainerInterface
    {
        $instance = $this->marshalNewInstance();
        $interfaces = empty($interfaces) ? array_keys($this->services) : $interfaces;

        if ( ! $this->has(... $interfaces) ) {
            throw new ServiceContainerException('One or more interfaces are not known');
        }

        foreach ( $interfaces as $current ) {
            $instance->services[$this->marshalKey($current)] = new ProtectedService(
                $this->services[$this->marshalKey($current)]
            );
        }

        return $instance;
    }

    /**
     * clones the instance. Optional provided interfaces are filtered from new container instance. When no interface
     * is provided, the new container will have the entire containment of the expelled container assigned as
     * protected services.
     *
     * @param \string[] ...$interfaces
     * @return ContainerInterface|ServiceContainerInterface
     */
    public function expel(string ... $interfaces): ContainerInterface
    {
        $instance = $this->marshalNewInstance();
        $ensuredInterfaces = array_diff(
            array_keys($this->services),
            array_map(
                function(string $in) {
                    return $this->marshalKey($in);
                },
                $interfaces
            )
        );

        foreach ( $ensuredInterfaces as $current ) {
            $instance->services[$current] = new ProtectedService($this->services[$current]);
        }

        return $instance;
    }

    /**
     * registers the provided service providers.
     *
     * @param ServiceProviderInterface[] ...$providers
     * @return ServiceContainerInterface
     */
    public function register(ServiceProviderInterface ... $providers): ServiceContainerInterface
    {
        foreach ( $providers as $current ) {
            /** @var ServiceContainerInterface $this */
            $current->services($this);
        }
    }

    /**
     * resolves the provided interface to a service instance.
     *
     * @param string $interface
     * @return ServiceInterface
     */
    protected function resolveInterface(string $interface): ServiceInterface
    {
        if ( $this->has($interface) ) {
            return $this->get($interface);
        }

        return parent::resolveInterface($interface);
    }

    /**
     * marshals the service key.
     *
     * @param string $interface
     * @return string
     */
    protected function marshalKey(string $interface): string
    {
        return trim(strtolower($interface), "\\");
    }

    /**
     * marshals a new instance of the dependency injection container.
     *
     * @return DependencyInjectionContainer
     */
    protected function marshalNewInstance(): DependencyInjectionContainer
    {
        return new static;
    }
}