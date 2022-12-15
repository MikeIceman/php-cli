<?php

namespace MikeIceman\Helpers\Tests;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\Constraint\LogicalAnd;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Parent class for all tests in suite.
 */
abstract class BaseTestCase extends TestCase
{
    /**
     * @var Generator|null
     */
    private ?Generator $faker;

    /**
     * Returns faker generator object that can create data for tests.
     *
     * @return Generator
     */
    protected function getFaker(): Generator
    {
        if ($this->faker === null) {
            $this->faker = Factory::create();
        }

        return $this->faker;
    }

    /**
     * Constraint to check that array has specified attributes.
     *
     * @param array $attributes
     *
     * @return LogicalAnd
     */
    public function arrayHasAttributes(array $attributes): LogicalAnd
    {
        return $this->logicalAnd(
            $this->callback(
                function ($array) use ($attributes) {
                    $hasAttributes = true;
                    foreach ($attributes as $name => $value) {
                        $this->assertArrayHasKey($name, $array, sprintf('Key "%s" not found in array', $name));
                        $this->assertEquals(
                            $value,
                            $array[$name],
                            sprintf('Attribute name: "%s". Attribute value: "%s".', $name, $value)
                        );

                        if ($array[$name] !== $value) {
                            $hasAttributes = false;
                            break;
                        }
                    }

                    return $hasAttributes;
                }
            )
        );
    }

    /**
     * Constraint to check that object has specified attributes.
     *
     * @param array $attributes
     *
     * @return LogicalAnd
     */
    public function objectHasAttributes(array $attributes): LogicalAnd
    {
        return $this->logicalAnd(
            $this->callback(
                function ($object) use ($attributes) {
                    $hasAttributes = true;
                    foreach ($attributes as $name => $value) {
                        $this->assertObjectHasAttribute($name, $object,
                            sprintf('Attribute "%s" not found in object', $name)
                        );
                        $this->assertEquals(
                            $value,
                            $object->$name,
                            sprintf('Attribute name: "%s". Attribute value: "%s".', $name, $value)
                        );

                        if ($object->$name !== $value) {
                            $hasAttributes = false;
                            break;
                        }
                    }

                    return $hasAttributes;
                }
            )
        );
    }


    /**
     * @param string $className
     * @param int $id
     * @param string $title
     * @param MockObject[] $childs
     *
     * @return MockObject
     */
    public function getObject(string $className = '', int $id = 1, string $title = '', array $childs = []): MockObject
    {
        $mock = $this->getMockBuilder($className)->getMock();

        $mock->method('getId')->willReturn($id);
        $mock->method('getTitle')->willReturn($title);
        $mock->method('getChilds')->willReturn($childs);

        return $mock;
    }
}
