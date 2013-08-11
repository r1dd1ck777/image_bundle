<?php

namespace Rid\Bundle\ImageBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RidImageTestCase extends WebTestCase
{
    /** @var \appTestDebugProjectContainer */
    protected $_container;

    /** @var \AppKernel */
    protected $_kernel;

    /** @var \Doctrine\ORM\EntityManager */
    protected $_em;

    protected $_client;

    public function init()
    {
        $this->_client = static::createClient();

        $this->_container = $this->_client->getContainer();

        $this->_kernel = $this->_client->getKernel();

        $this->_em = $this->_container->get('doctrine.orm.entity_manager');
    }
}
