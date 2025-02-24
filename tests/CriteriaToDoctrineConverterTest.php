<?php

namespace MarioDevv\Criteria\Tests;

use CodelyTv\Criteria\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use MarioDevv\Criteria\CriteriaToDoctrineConverter;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Criteria as DoctrineCriteria;

class CriteriaToDoctrineConverterTest extends TestCase
{

    /** @test */
    public function it_should_create_a_doctrine_criteria_from_a_criteria_object()
    {
        $criteria = Criteria::fromPrimitives(
            [
                ['field' => 'name', 'operator' => 'CONTAINS', 'value' => 'Mario'],
                ['field' => 'id', 'operator' => '=', 'value' => '1'],
            ],
            'id',
            'asc',
            10,
            1
        );

        $expectedDoctrineCriteria = new DoctrineCriteria(
            new CompositeExpression(
                CompositeExpression::TYPE_AND,
                [
                    new Comparison('name', 'CONTAINS', 'Mario'),
                    new Comparison('id', '=', 1),
                ]
            ),
            ['id' => 'asc'],
            0,
            10
        );


        $criteriaToDoctrineConverter = new CriteriaToDoctrineConverter();
        $doctrineCriteria            = $criteriaToDoctrineConverter->convert($criteria);


        $this->assertEquals($expectedDoctrineCriteria, $doctrineCriteria);
    }


    /** @test */
    public function it_should_create_a_doctrine_criteria_from_a_criteria_object_with_hydrators()
    {
        $criteria = Criteria::fromPrimitives(
            [
                ['field' => 'name', 'operator' => 'CONTAINS', 'value' => 'Mario'],
                ['field' => 'id', 'operator' => '=', 'value' => '18289'],
            ],
            'id',
            'asc',
            10,
            2
        );

        $expectedDoctrineCriteria = new DoctrineCriteria(
            new CompositeExpression(
                CompositeExpression::TYPE_AND,
                [
                    new Comparison('name', 'CONTAINS', 'Mario'),
                    new Comparison('id', '=', [18289]),
                ]
            ),
            ['id' => 'asc'],
            10,
            10
        );

        $criteriaToDoctrineConverter = new CriteriaToDoctrineConverter(
            ['id' => 'id'],
            ['id' => fn($value) => (array)$value]
        );
        $doctrineCriteria            = $criteriaToDoctrineConverter->convert($criteria);

        $this->assertEquals($expectedDoctrineCriteria, $doctrineCriteria);
    }


    /** @test */
    public function it_should_be_able_to_paginate_only_with_page_size(): void
    {

        $criteria = Criteria::fromPrimitives(
            [
                ['field' => 'name', 'operator' => 'CONTAINS', 'value' => 'Mario'],
                ['field' => 'id', 'operator' => '=', 'value' => '1'],
            ],
            'id',
            'asc',
            10,
            null
        );

        $expectedDoctrineCriteria = new DoctrineCriteria(
            new CompositeExpression(
                CompositeExpression::TYPE_AND,
                [
                    new Comparison('name', 'CONTAINS', 'Mario'),
                    new Comparison('id', '=', 1),
                ]
            ),
            ['id' => 'asc'],
            null,
            10
        );


        $criteriaToDoctrineConverter = new CriteriaToDoctrineConverter();
        $doctrineCriteria            = $criteriaToDoctrineConverter->convert($criteria);


        $this->assertEquals($expectedDoctrineCriteria, $doctrineCriteria);

    }
}
