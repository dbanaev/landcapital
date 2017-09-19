<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\SearchBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SIPSearchExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        /**
         * Indexer.
         */
        if( isset($config['indexer']) ) {
            $container->setParameter('search.sphinxsearch.indexer.bin', $config['indexer']['bin']);
        }

        /**
         * Indexes.
         */
        $indexes = array();
        foreach( $config['indexes'] as $label => $index ) {
            foreach( $index as $name => $fields ) {
                if( !isset($indexes[$label]) )
                    $indexes[$label] = array('index_name' => $name, 'field_weights' => array());

                foreach( $fields as $field => $weight )
                    $indexes[$label]['field_weights'][$field] = $weight;
            }
        }
        $container->setParameter('search.sphinxsearch.indexes', $indexes);

        /**
         * Searchd.
         */
        if( isset($config['searchd']) ) {
            $container->setParameter('search.sphinxsearch.searchd.host', $config['searchd']['host']);
            $container->setParameter('search.sphinxsearch.searchd.port', $config['searchd']['port']);
            $container->setParameter('search.sphinxsearch.searchd.socket', $config['searchd']['socket']);
        }

        // This check is added because of strange symfony behaviour. It can includes SphinxAPI somewhere else
        // I couldn't realise where. Shams <info@shams.su>
        if(!class_exists('\SphinxClient')) {

            $searchServiceDefinition = $container->getDefinition('search.sphinxsearch.search');
            $searchServiceDefinition->setFile(realpath(__DIR__ . '/../Services/Search/SphinxAPI.php'));
        }
    }
}
