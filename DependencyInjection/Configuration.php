<?php

namespace Rid\Bundle\ImageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('rid_image');

        $rootNode
            ->children()
                ->arrayNode('dir')
                    ->children()
                        ->scalarNode('web')->defaultValue("%kernel.root_dir%/../web/")->end()
                    ->end()
                ->end()
                ->arrayNode('path')
                    ->children()
                        ->scalarNode('tmp')->defaultValue("uploads/tmp/")->end()
                    ->end()
                ->end()
                ->arrayNode('presets')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('your_preset_name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('thumbnails')
//                                ->isRequired()
//                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('your_thumbnail_name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('default')->end()
                                        ->integerNode('width')->min(1)->max(2000)->isRequired()->end()
                                        ->integerNode('height')->min(1)->max(2000)->isRequired()->end()
                                        ->scalarNode('type')
//                                            ->defaultValue('outbound')->validate()->ifNotInArray(array('outbound', 'inset'))->thenInvalid('Invalid type %s (use outbound or inset)')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('path')->defaultValue("uploads/")->end()
                            ->booleanNode('save_name')
                                ->defaultFalse()
                            ->end()
                            ->scalarNode('default')->end()
                            ->arrayNode('jcrop')
                                ->children()
                                    ->scalarNode('width')->isRequired()->defaultValue(150)->end()
                                    ->scalarNode('height')->isRequired()->defaultValue(150)->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('fields')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('className')
                    ->prototype('array')
//                        ->children()
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('propertyName')
                            ->prototype('scalar')
                            ->end()
//                        ->end()
                    ->end()
                ->end()
            ->end();
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }
}
