<?php

namespace PatchManager\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('patch_manager');
        $rootNode
            ->children()
                ->scalarNode('alias')
                    ->defaultNull()
                    ->validate()
                    ->ifTrue(function ($value) { return preg_match('#^[a-zA-Z0-9_]+$#', $value) === 0; })
                        ->thenInvalid('Alias should be a string containing letters and underscore. "%s" given')
                    ->end()
                ->end()
                ->scalarNode('dispatch_events')->defaultTrue()->end()
            ->end();

        return $treeBuilder;
    }
}
