<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\Bridge\Symfony\App\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Nucleos\Doctrine\Migration\IdToUuidMigration;
use Psr\Log\LoggerInterface;

final class Version_2_0_0 extends AbstractMigration
{
    private IdToUuidMigration $idToUuidMigration;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        parent::__construct($connection, $logger);

        $this->idToUuidMigration = new IdToUuidMigration($this->connection, $logger);
    }

    public function getDescription(): string
    {
        return 'UUID migration';
    }

    public function up(Schema $schema): void
    {
        $this->idToUuidMigration->migrate('acme__meal');
        $this->idToUuidMigration->migrate('acme__ingredient');
        $this->idToUuidMigration->migrate('acme__attribute');
        $this->idToUuidMigration->migrate('acme__category');
        $this->idToUuidMigration->migrate('acme__page');
        $this->idToUuidMigration->migrate('acme__menu');
        $this->idToUuidMigration->migrate('acme__page_category');
    }
}
