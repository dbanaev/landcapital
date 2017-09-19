<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\ResourceBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Intl\Intl;

class SettingsExtension extends \Twig_Extension
{
    protected $rates;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            'get_setting'   => new \Twig_Function_Method($this, 'getSetting'),
            'get_footer'    =>  new \Twig_Function_Method($this, 'getFooter'),
            'get_favorites' =>  new \Twig_Function_Method($this, 'getFavorites'),
            'to_favorites'  => new \Twig_Function_Method($this, 'getToFavorites'),
            'currency'      => new \Twig_Function_Method($this, 'currencyFunction'),
            'calculateRate' => new \Twig_Function_Method($this, 'getRate'),
            'get_icon_by_type' => new \Twig_Function_Method($this, 'getIconByType'),
        );
    }

    /**
     * @param string $code
     * @return int
     */
    public function getSetting ($code) {
        return $this->container->get('sip_resource.helper.settings')->get($code);
    }
    
    /**     * 
     * @return array Objects
     */
    public function getFooter () {
        $em = $this->container->get('doctrine')->getManager();
        $selections = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Selection')->getSelectObjects(10);
        return ['selections'=>$selections];
    }
    
    
    /**     * 
     * @return array Objects
     */
    public function getFavorites () {
        return isset($_COOKIE['feature']) ? count(json_decode($_COOKIE['feature'], true)) : 0;
    }
    
    /**     * 
     * @return array Objects
     */
    public function getToFavorites ($id = 0) {
        $favoritrs = isset($_COOKIE['feature']) ? json_decode($_COOKIE['feature'], true) : [];
        return in_array($id, $favoritrs);
    }

    /**
     * @param $locale
     * @return bool|string
     */
    public function currencyFunction($currency)
    {
        if ($currency == 'RUR')
            return '₽';
        else
            return Intl::getCurrencyBundle()->getCurrencySymbol($currency, 'ru');
    }

    /**
     * @param $currency
     * @param $value
     * @return bool|float
     */
    public function getRate($currency, $value)
    {
        if (!isset($this->rates[$currency])) {
            $em = $this->container->get('doctrine')->getManager();
            /** @var \SIP\ResourceBundle\Entity\Lists\Currency $rate */
            $rate = $em->getRepository('SIP\ResourceBundle\Entity\Lists\Currency')->findOneByCode($currency);
            if ($rate) {
                $this->rates[$currency] = $rate->getRatio();
            }
        }
        if (isset($this->rates[$currency])) {
            return (float) $value / $this->rates[$currency];
        }
        return false;
    }

    public function getIconByType($type)
    {
        switch ($type) {
            case 'house':
                return 'fa-home';
            case 'townhouse':
                return 'fa-building-o';
            case 'flat':
                return 'fa-building';
            case 'land':
                return 'fa-tree';
        }
        return 'fa-home';
    }


    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'settings';
    }
}