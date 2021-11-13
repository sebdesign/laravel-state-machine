<?php

namespace Sebdesign\SM\Metadata;

use Illuminate\Support\Arr;
use SM\SMException;

class MetadataStore implements MetadataStoreInterface
{
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function graph($key = null, $default = null)
    {
        return $this->get($this->config, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function state($state, $key = null, $default = null)
    {
        foreach ($this->config['states'] as $value) {
            if ($value['name'] == $state) {
                return $this->get($value, $key, $default);
            }
        }

        throw new SMException(sprintf(
            'State "%s" does not exist on graph "%s".',
            $state,
            $this->config['graph']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function transition($transition, $key = null, $default = null)
    {
        if (! isset($this->config['transitions'][$transition])) {
            throw new SMException(sprintf(
                'Transition "%s" does not exist on graph "%s"',
                $transition,
                $this->config['graph']
            ));
        }

        return $this->get($this->config['transitions'][$transition], $key, $default);
    }

    /**
     * Get a metadata value from a subject.
     *
     * @param  array  $subject
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    protected function get(array $subject, $key, $default)
    {
        $metadata = Arr::get($subject, 'metadata', []);

        return Arr::get($metadata, $key, $default);
    }
}
