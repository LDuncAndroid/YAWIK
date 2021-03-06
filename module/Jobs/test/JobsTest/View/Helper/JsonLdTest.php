<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace JobsTest\View\Helper;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Jobs\Entity\Job;
use Jobs\View\Helper\JsonLd;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;
use Laminas\View\Helper\AbstractHelper;

/**
 * Tests for \Jobs\View\Helper\JsonLd
 *
 * @covers \Jobs\View\Helper\JsonLd
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.View
 * @group Jobs.View.Helper
 */
class JsonLdTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = JsonLd::class;

    private $inheritance = [ AbstractHelper::class ];

    public function propertiesProvider()
    {
        $job = new Job();
        return [
            ['job', [
                'value' => $job,
                'expect_property' => $job,
            ]]
        ];
    }

    public function testReturnsNothingIfJobIsNull()
    {
        $this->assertEmpty($this->target->__invoke());
    }
    
    public function testReturnsNothingIfJobIsNotActice()
    {
        $job = new Job();
        $organization = new Organization();
        $name = new OrganizationName('test');
        $organization->setOrganizationName($name);
        $job->setOrganization($organization);
        $job->setTitle('Test JsonLd view helper');
        $job->setDatePublishStart(new \DateTime());
        $this->assertEmpty($this->target->__invoke());
    }

    public function testReturnsJsonLd()
    {
        $job = new Job();
        $job->setStatus(\Jobs\Entity\Status::ACTIVE);
        $organization = new Organization();
        $name = new OrganizationName('test');
        $organization->setOrganizationName($name);
        $job->setOrganization($organization);
        $job->setTitle('Test JsonLd view helper');
        $job->setDatePublishStart(new \DateTime());

        $json = $this->target->__invoke($job);

        $this->assertStringStartsWith('<script type="application/ld+json">', $json);
        $this->assertStringEndsWith('</script>', $json);
        $this->assertContains('"title":"Test JsonLd view helper"', $json);
    }
}
