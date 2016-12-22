<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineDataFixtureModule;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use DoctrineDataFixtureModule\Command\ImportCommand;

/**
 * Base module for Doctrine Data Fixture.
 *
 * @license MIT
 * @link    www.doctrine-project.org
 * @author  Martin Shwalbe <martin.shwalbe@gmail.com>
 */
class Module implements ConfigProviderInterface, InitProviderInterface, ServiceProviderInterface
{

    /**
     * {@inheritDoc}
     */
    public function init(ModuleManagerInterface $manager)
    {
        $manager->getEventManager()->getSharedManager()->attach(
            'doctrine',
            'loadCli.post',
            [$this, 'initCli']
        );
    }

    /**
     * Initialise the command line interface
     *
     * @param EventInterface $event
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function initCli(EventInterface $event)
    {
        $application    = $event->getTarget();
        $serviceManager = $event->getParam('ServiceManager');
        $entityManager  = $application->getHelperSet()->get('em')->getEntityManager();
        $paths          = $serviceManager->get('doctrine.configuration.fixtures');

        $importCommand = new ImportCommand($serviceManager);
        $importCommand->setEntityManager($entityManager);
        $importCommand->setPath($paths);
        ConsoleRunner::addCommands($application);
        $application->addCommands([$importCommand]);
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceConfig()
    {
        return include __DIR__ . '/../config/services.config.php';
    }
}
