<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\ResourceBundle\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class SettingsHelper extends Helper
{
    protected $settings;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'settings';
    }

    public function get($code)
    {
        if(!isset($this->settings[$code])){
            $this->settings[$code] = false;
            $setting = $this->container
                ->get('doctrine')
                ->getRepository('SIP\ResourceBundle\Entity\Setting')
                ->findOneByCode($code);        
            if($setting) {
                $this->settings[$code] = $setting;
            }else{
                $this->settings[$code]['name'] = $this->settings[$code]['value'] = '';
            }
        }

        return $this->settings[$code];
    }
}

