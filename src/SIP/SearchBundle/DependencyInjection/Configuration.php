<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\SearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sip_search');

        $this->addIndexerSection($rootNode);
        $this->addIndexesSection($rootNode);
        $this->addSearchdSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    public function addIndexerSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('indexer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('bin')->defaultValue('/usr/bin/indexer')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    public function addIndexesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('indexes')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                    ->useAttributeAsKey('key')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    public function addSearchdSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('searchd')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->scalarNode('port')->defaultValue('9312')->end()
                        ->scalarNode('socket')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();
    }
}
