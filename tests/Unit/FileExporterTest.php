<?php
declare(strict_types=1);

namespace phppio\tests\Unit;

use phppio\export\FileExporter;
use PHPUnit\Framework\TestCase;

class FileExporterTest extends TestCase
{
    protected function setUp()
    {
        register_shutdown_function(function () {
            if (file_exists('temp.file')) {
                unlink('temp.file');
            }
        });
    }

    public function testExport()
    {
        $exporter = new FileExporter('temp.file');
        $exporter->createEvent('event-1', 'entity-type-1', 'entity-id-1',
            null, null, null, ['date' => '2015-04-01']);
        $exporter->createEvent('event-2', 'entity-type-2', 'entity-id-2',
            new \DateTime('2015-04-01'), 'target-entity-type-2', 'target-entity-id-2', ['property' => 'blue']);

        $exported = file_get_contents('temp.file');

        $date = new \DateTime('2015-04-01');
        $expectedDate = $date->format(\DateTime::ATOM);

        $expected =<<<EOS
{"event":"event-1","entityType":"entity-type-1","entityId":"entity-id-1","eventTime":"$expectedDate"}
{"event":"event-2","entityType":"entity-type-2","entityId":"entity-id-2","eventTime":"$expectedDate","targetEntityType":"target-entity-type-2","targetEntityId":"target-entity-id-2","properties":{"property":"blue"}}

EOS;

        $this->assertEquals($expected, $exported);
    }
}
