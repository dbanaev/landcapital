<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\ResourceBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    /**
     * @param FactoryInterface $factory
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');
        $menu->setAttribute('class', 'menu');
        $menu->setChildrenAttributes(array('class' => 'menu-item menu-item-type-post_type menu-item-object-page'));

        $menu->addChild('sip_resource_residential_estate',
            array(
                'route' => 'sip_resource_residential_estate',
            )
        );

        $menu->addChild('sip_resource_rent',
            array(
                'route' => 'sip_resource_rent',
            )
        );

        $menu->addChild('sip_resource_about',
            array(
                'route' => 'sip_resource_main_about',
            )
        );

        $menu->addChild('sip_resource_contacts',
            array(
                'route' => 'sip_resource_main_contacts',
            )
        );

        return $menu;
    }
}