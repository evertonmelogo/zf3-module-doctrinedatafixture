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
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */
namespace DoctrineDataFixtureTest\Loader;

use Doctrine\Common\DataFixtures\FixtureInterface;
use DoctrineDataFixtureModule\Loader\ServiceLocatorAwareLoader;
use DoctrineDataFixtureTest\TestAsset\Fixtures\HasSL\FixtureA as FixedHasSL;
use DoctrineDataFixtureTest\TestAsset\Fixtures\NoSL\FixtureA as FixtureNoSL;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Mvc\Service\ServiceManagerConfig;

/**
 * Test Service Locator-aware fixture loader
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @author  Adam Lundrigan <adam@lundrigan.ca>
 */
class ServiceLocatorAwareLoaderTest extends PHPUnit_Framework_TestCase
{

    /**
     * Ensures that ServiceLocatorAwareLoader does not affect loading of
     * fixtures that are not SL-aware
     */
    public function testLoadingFixtureWhichIsNotServiceLocatorAware()
    {
        $fixtureClassName = FixtureNoSL::class;
        $serviceConfig    = new ServiceManagerConfig;
        $serviceLocator   = new ServiceManager($serviceConfig);
        
        $loader = new ServiceLocatorAwareLoader($serviceLocator);
        $loader->loadFromDirectory(__DIR__ . '/../TestAsset/Fixtures/NoSL');
        $fixtures = $loader->getFixtures();
        
        $this->assertArrayHasKey($fixtureClassName, $fixtures);
        $fixture = $fixtures[$fixtureClassName];
        
        $this->assertInstanceOf(FixtureInterface::class, $fixture);
        $this->assertNotInstanceOf(ServiceLocatorAwareInterface::class, $fixture);
    }
    
    /**
     * Ensures that the Service Locator instance passed into the ServiceLocatorAwareLoader
     * actually makes it to the SL-aware fixtures loaded
     */
    public function testLoadingFixtureWhichIsServiceLocatorAware()
    {
        $fixtureClassName = FixedHasSL::class;
        $serviceConfig    = new ServiceManagerConfig;
        $serviceLocator   = new ServiceManager($serviceConfig);
        
        $loader = new ServiceLocatorAwareLoader($serviceLocator);
        $loader->loadFromDirectory(__DIR__ . '/../TestAsset/Fixtures/HasSL');
        $fixtures = $loader->getFixtures();
    
        $this->assertArrayHasKey($fixtureClassName, $fixtures);
        $fixture = $fixtures[$fixtureClassName];
        
        $this->assertInstanceOf(FixtureInterface::class, $fixture);
        $this->assertInstanceOf(ServiceLocatorAwareInterface::class, $fixture);
        $this->assertSame($serviceLocator, $fixture->getServiceLocator());
    }
}
