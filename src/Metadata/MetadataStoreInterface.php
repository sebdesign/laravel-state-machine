<?php

namespace Sebdesign\SM\Metadata;

interface MetadataStoreInterface
{
    /**
     * Get metadata from the graph.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function graph($key = null, $default = null);

    /**
     * Get metadata from a state.
     *
     *
     * @param  string  $state
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     *
     * @throws \SM\SMException If the state does not exist.
     */
    public function state($state, $key = null, $default = null);

    /**
     * Get metadata from a transaction.
     *
     *
     * @param  string  $transition
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     *
     * @throws \SM\SMException If the transition does not exist.
     */
    public function transition($transition, $key = null, $default = null);
}
