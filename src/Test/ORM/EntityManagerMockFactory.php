<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Test\ORM;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Version;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class EntityManagerMockFactory
{
    /**
     * @param TestCase $test
     * @param \Closure $qbCallback
     * @param mixed    $fields
     *
     * @return MockObject|EntityManager
     */
    public static function create(TestCase $test, \Closure $qbCallback, $fields): MockObject
    {
        $query = $test->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()->getMock();
        $query->method('execute')->will($test->returnValue(true));

        if (Version::compare('2.5.0') < 1) {
            $entityManager = $test->getMockBuilder(EntityManagerInterface::class)->getMock();
            $qb            = $test->getMockBuilder(QueryBuilder::class)->setConstructorArgs([$entityManager])->getMock();
        } else {
            $qb = $test->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        }

        $qb->method('select')->will($test->returnValue($qb));
        $qb->method('getQuery')->will($test->returnValue($query));
        $qb->method('where')->will($test->returnValue($qb));
        $qb->method('orderBy')->will($test->returnValue($qb));
        $qb->method('andWhere')->will($test->returnValue($qb));
        $qb->method('leftJoin')->will($test->returnValue($qb));

        $qbCallback($qb);

        $repository = $test->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $repository->method('createQueryBuilder')->will($test->returnValue($qb));

        $metadata = $test->getMockBuilder(ClassMetadataInfo::class)->disableOriginalConstructor()->getMock();
        $metadata->method('getFieldNames')->will($test->returnValue($fields));
        $metadata->method('getName')->will($test->returnValue('className'));
        $metadata->method('getIdentifier')->will($test->returnValue(['id']));
        $metadata->method('getTableName')->will($test->returnValue('dummy'));

        $connection = $test->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();

        $em = $test->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->method('getRepository')->will($test->returnValue($repository));
        $em->method('getClassMetadata')->will($test->returnValue($metadata));
        $em->method('getConnection')->will($test->returnValue($connection));

        return $em;
    }
}
