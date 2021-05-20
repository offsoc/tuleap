<?php
/**
 * Copyright (c) Enalean, 2021 - present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tuleap\Tracker\Semantic\Timeframe;

use Tuleap\Tracker\REST\SemanticTimeframeWithEndDateRepresentation;

class TimeframeWithEndDateTest extends \Tuleap\Test\PHPUnit\TestCase
{
    /**
     * @var TimeframeWithEndDate
     */
    private $timeframe;
    /**
     * @var \Tracker_FormElement_Field_Date
     */
    private $start_date_field;
    /**
     * @var \Tracker_FormElement_Field_Date
     */
    private $end_date_field;

    protected function setUp(): void
    {
        $this->start_date_field = $this->getMockedDateField(1001);
        $this->end_date_field   = $this->getMockedDateField(1003);

        $this->timeframe = new TimeframeWithEndDate(
            $this->start_date_field,
            $this->end_date_field
        );
    }

    /**
     * @testWith [1001, true]
     *           [1002, false]
     *           [1003, true]
     */
    public function testItReturnsTrueWhenFieldIsUsed(int $field_id, bool $is_used): void
    {
        $field = $this->getMockedDateField($field_id);

        $this->assertEquals(
            $is_used,
            $this->timeframe->isFieldUsed($field)
        );
    }

    public function testItReturnsItsConfigDescription(): void
    {
        $this->start_date_field->expects($this->any())->method('getLabel')->will(self::returnValue('Start date'));
        $this->end_date_field->expects($this->any())->method('getLabel')->will(self::returnValue('End date'));

        $this->assertEquals(
            'Timeframe is based on start date field &quot;Start date&quot; and end date field &quot;End date&quot;.',
            $this->timeframe->getConfigDescription()
        );
    }

    public function testItIsDefined(): void
    {
        $this->assertTrue($this->timeframe->isDefined());
    }

    public function testItDoesNotExportToXMLIfStartDateIsNotInFieldMapping(): void
    {
        $root = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tracker />');
        $this->timeframe->exportToXml($root, []);

        $this->assertCount(0, $root->children());
    }

    public function testItDoesNotExportToXMLIfEndDateIsNotInFieldMapping(): void
    {
        $root = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tracker />');
        $this->timeframe->exportToXml($root, [
            'F101' => 1001
        ]);

        $this->assertCount(0, $root->children());
    }

    public function testItExportsToXML(): void
    {
        $root = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tracker />');
        $this->timeframe->exportToXml($root, [
            'F101' => 1001,
            'F102' => 1003,
        ]);

        $this->assertCount(1, $root->children());
        $this->assertEquals('timeframe', (string) $root->semantic['type']);
        $this->assertEquals('F101', (string) $root->semantic->start_date_field['REF']);
        $this->assertEquals('F102', (string) $root->semantic->end_date_field['REF']);
    }

    /**
     * @testWith [false, false]
     *           [true, false]
     */
    public function testItDoesNotExportToRESTWhenUserCanReadFields(bool $can_read_start_date, bool $can_read_end_date): void
    {
        $user = $this->getMockBuilder(\PFUser::class)->disableOriginalConstructor()->getMock();
        $this->start_date_field->expects(self::any())->method('userCanRead')->will(self::returnValue($can_read_start_date));
        $this->end_date_field->expects(self::any())->method('userCanRead')->will(self::returnValue($can_read_end_date));

        $this->assertNull($this->timeframe->exportToREST($user));
    }

    public function testItExportsToREST(): void
    {
        $user = $this->getMockBuilder(\PFUser::class)->disableOriginalConstructor()->getMock();
        $this->start_date_field->expects(self::any())->method('userCanRead')->will(self::returnValue(true));
        $this->end_date_field->expects(self::any())->method('userCanRead')->will(self::returnValue(true));

        $this->assertEquals(
            new SemanticTimeframeWithEndDateRepresentation(
                1001,
                1003
            ),
            $this->timeframe->exportToREST($user)
        );
    }

    public function testItSaves(): void
    {
        $dao     = $this->getMockBuilder(SemanticTimeframeDao::class)->disableOriginalConstructor()->getMock();
        $tracker = $this->getMockBuilder(\Tracker::class)->disableOriginalConstructor()->getMock();

        $dao->expects(self::once())->method('save')->with(113, 1001, null, 1003)->will(self::returnValue(true));
        $tracker->expects(self::once())->method('getId')->will(self::returnValue(113));

        self::assertTrue(
            $this->timeframe->save($tracker, $dao)
        );
    }

    private function getMockedDateField(int $field_id): \Tracker_FormElement_Field_Date
    {
        $mock = $this->getMockBuilder(\Tracker_FormElement_Field_Date::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())->method('getId')->will($this->returnValue($field_id));

        return $mock;
    }
}
