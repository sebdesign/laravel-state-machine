<?php

namespace Sebdesign\Sm\Test\Metadata;

use SM\SMException;
use Sebdesign\SM\Test\TestCase;
use Sebdesign\SM\Metadata\MetadataStore;

class MetadataStoreTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_the_graph_metadata()
    {
        $metadata = new MetadataStore([
            'graph' => 'default',
            'metadata' => ['title' => 'Graph'],
        ]);

        $this->assertEquals(['title' => 'Graph'], $metadata->graph());
        $this->assertEquals('Graph', $metadata->graph('title'));

        $this->assertNull($metadata->graph('invalid'));
        $this->assertFalse($metadata->graph('invalid', false));
    }

    /**
     * @test
     */
    public function it_gets_the_state_metadata()
    {
        $metadata = new MetadataStore([
            'graph' => 'default',
            'states' => [
                [
                    'name' => 'new',
                    'metadata' => ['title' => 'New'],
                ],
            ],
        ]);

        $this->assertEquals(['title' => 'New'], $metadata->state('new'));
        $this->assertEquals('New', $metadata->state('new', 'title'));

        $this->assertNull($metadata->state('new', 'description'));
        $this->assertFalse($metadata->state('new', 'description', false));

        try {
            $metadata->state('invalid');

            $this->fail('The MetadataStore should not get metadata from invalid states.');
        } catch (SMException $e) {
            $this->assertEquals('State "invalid" does not exist on graph "default".', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_gets_the_transition_metadata()
    {
        $metadata = new MetadataStore([
            'graph' => 'default',
            'transitions' => [
                'ask_for_changes' => [
                    'metadata' => ['title' => 'Ask for changes'],
                ],
            ],
        ]);

        $this->assertEquals(['title' => 'Ask for changes'], $metadata->transition('ask_for_changes'));
        $this->assertEquals('Ask for changes', $metadata->transition('ask_for_changes', 'title'));

        $this->assertNull($metadata->transition('ask_for_changes', 'description'));
        $this->assertFalse($metadata->transition('ask_for_changes', 'description', false));

        try {
            $metadata->transition('invalid');
            $this->fail('The MetadataStore should not get metadata for invalid transitions');
        } catch (SMException $e) {
            $this->assertEquals('Transition "invalid" does not exist on graph "default"', $e->getMessage());
        }
    }
}
