<?php

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

final class EntityManagerMockFactory
{
    /**
     * @param \PHPUnit_Framework_TestCase $test
     * @param \Closure                    $qbCallback
     * @param mixed                       $fields
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|EntityManagerInterface
     */
    public static function create(\PHPUnit_Framework_TestCase $test, \Closure $qbCallback, $fields): \PHPUnit_Framework_MockObject_MockObject
    {
        $query = $test->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()->getMock();
        $query->expects($test->any())->method('execute')->will($test->returnValue(true));

        if (Version::compare('2.5.0') < 1) {
            $entityManager = $test->getMockBuilder(EntityManagerInterface::class)->getMock();
            $qb            = $test->getMockBuilder(QueryBuilder::class)->setConstructorArgs(array($entityManager))->getMock();
        } else {
            $qb = $test->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        }

        $qb->expects($test->any())->method('select')->will($test->returnValue($qb));
        $qb->expects($test->any())->method('getQuery')->will($test->returnValue($query));
        $qb->expects($test->any())->method('where')->will($test->returnValue($qb));
        $qb->expects($test->any())->method('orderBy')->will($test->returnValue($qb));
        $qb->expects($test->any())->method('andWhere')->will($test->returnValue($qb));
        $qb->expects($test->any())->method('leftJoin')->will($test->returnValue($qb));

        $qbCallback($qb);

        $repository = $test->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $repository->expects($test->any())->method('createQueryBuilder')->will($test->returnValue($qb));

        $metadata = $test->getMockBuilder(ClassMetadataInfo::class)->disableOriginalConstructor()->getMock();
        $metadata->expects($test->any())->method('getFieldNames')->will($test->returnValue($fields));
        $metadata->expects($test->any())->method('getName')->will($test->returnValue('className'));
        $metadata->expects($test->any())->method('getIdentifier')->will($test->returnValue('id'));
        $metadata->expects($test->any())->method('getTableName')->will($test->returnValue('dummy'));

        $connection = $test->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();

        $em = $test->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($test->any())->method('getRepository')->will($test->returnValue($repository));
        $em->expects($test->any())->method('getClassMetadata')->will($test->returnValue($metadata));
        $em->expects($test->any())->method('getConnection')->will($test->returnValue($connection));

        return $em;
    }
}
