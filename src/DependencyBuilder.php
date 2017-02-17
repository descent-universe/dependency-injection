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


use Descent\Services\Container\DependencyBuilderInterface;
use Descent\Services\Entities\Service;
use Descent\Services\Entities\ServiceInterface;
use Descent\Services\Exceptions\DependencyBuilderException;

class DependencyBuilder implements DependencyBuilderInterface
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
    public function make(string $interface, array $parameters = [], string ... $enforcedOptionalParameters)
    {
        return $this->build($this->resolveInterface($interface), $parameters, ... $enforcedOptionalParameters);
    }

    /**
     * calls the provided callback. Optional $parameters content supersedes incubated parameters. Optionally enforces
     * the provided optional parameter names.
     *
     * @param callable $callback
     * @param array $parameters
     * @param \string[] ...$enforcedOptionalParameters
     * @return mixed
     */
    public function call(callable $callback, array $parameters = [], string ... $enforcedOptionalParameters)
    {
        $closure = encloseCallback($callback);
        $reflection = new \ReflectionFunction($closure);

        return call_user_func(
            $callback,
            ... $this->resolveParameters($parameters, $enforcedOptionalParameters, ... $reflection->getParameters())
        );
    }

    /**
     * incubates the provided service. Optional $parameters content supersedes assigned or incubated parameters.
     * Optionally enforces the provided optional parameter names.
     *
     * @param ServiceInterface $service
     * @param array $parameters
     * @param \string[] ...$enforcedOptionalParameters
     * @return object
     */
    public function build(ServiceInterface $service, array $parameters = [], string ... $enforcedOptionalParameters)
    {
        if ( $service->hasInstance() ) {
            return $service->getInstance();
        }

        foreach ( $service->getParameters() as $key => $value ) {
            if ( ! array_key_exists($key, $parameters) ) {
                $parameters[$key] = $value;
            }
        }

        $instance = $service->getConcrete() instanceof \Closure
            ? $this->call($service->getConcrete(), $parameters, ... $enforcedOptionalParameters)
            : $this->incubateObject($service->getConcrete(), $parameters, $enforcedOptionalParameters)
        ;

        if ( ! is_a($instance, $service->getInterface()) ) {
            throw new DependencyBuilderException(
                'Incubated instances does not implement the service interface'
            );
        }

        if ( $service->isSingleton() ) {
            $service->withInstance($instance);
        }

        return $instance;
    }

    /**
     * incubates the object from the provided concrete class name.
     *
     * @param string $concrete
     * @param array $parameters
     * @param array $enforce
     * @throws DependencyBuilderException when the provided concrete is not instantiable
     * @return object
     */
    protected function incubateObject(string $concrete, array $parameters, array $enforce)
    {
        $reflection = new \ReflectionClass($concrete);
        $dependencies = [];

        if ( $reflection->isInstantiable() ) {
            throw new DependencyBuilderException('Can not incubate concretes who are not instantiable');
        }

        if ( $constructor = $reflection->getConstructor() ) {
            $dependencies = $this->resolveParameters($parameters, $enforce, ... $constructor->getParameters());
        }

        return $reflection->newInstance(... $dependencies);
    }

    /**
     * resolves the provided reflections and considers the provided parameters and enforced parameters. Named Parameters
     * are used prior numeric-indexed parameters.
     *
     * @param array $parameters
     * @param array $enforce
     * @param \ReflectionParameter[] ...$reflections
     * @throws DependencyBuilderException when a reflection parameter is not resolvable
     * @return \Generator
     */
    protected function resolveParameters(array $parameters, array $enforce, \ReflectionParameter ... $reflections): \Generator
    {
        foreach ( $reflections as $current ) {
            /**
             * check for named parameters
             */
            if ( array_key_exists($current->getName(), $parameters) ) {
                yield $parameters[$current->getName()];
                continue;
            }

            /**
             * check for numeric indexed parameters
             */
            if ( array_key_exists($current->getPosition(), $parameters) ) {
                yield $parameters[$current->getPosition()];
                continue;
            }

            /**
             * check for class dependencies
             */
            if ( $class = $current->getClass() && ! $current->isOptional() ) {
                yield $this->make($class->getName());
                continue;
            }

            /**
             * check for enforced optional class dependencies
             */
            if ( $class = $current->getClass() && $current->isOptional() && in_array($current->getName(), $enforce) ) {
                yield $this->make($class->getName());
                continue;
            }

            /**
             * check for optional parameters and assign their default values
             */
            if ( $current->isOptional() ) {
                yield $current->getDefaultValue();
                continue;
            }

            /**
             * the parameter is unresolvable
             */
            throw new DependencyBuilderException('Can not resolve parameter: '.$current->getName());
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
        return new Service($interface, $interface);
    }
}