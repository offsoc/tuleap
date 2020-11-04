<?php
/**
 * Copyright (c) Enalean, 2020 - Present. All Rights Reserved.
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

declare(strict_types=1);

namespace Tuleap\ScaledAgile\Program\Backlog\AsynchronousCreation;

use Mockery as M;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tracker_FormElement_Field_List_Bind_StaticValue;
use Tuleap\ScaledAgile\Program\Backlog\ProgramIncrement\Source\Changeset\Values\ArtifactLinkValueData;
use Tuleap\ScaledAgile\Program\Backlog\ProgramIncrement\Source\Changeset\Values\DescriptionValueData;
use Tuleap\ScaledAgile\Program\Backlog\ProgramIncrement\Source\Changeset\Values\EndPeriodValueData;
use Tuleap\ScaledAgile\Program\Backlog\ProgramIncrement\Source\Changeset\Values\SourceChangesetValuesCollection;
use Tuleap\ScaledAgile\Program\Backlog\ProgramIncrement\Source\Changeset\Values\StartDateValueData;
use Tuleap\ScaledAgile\Program\Backlog\ProgramIncrement\Source\Changeset\Values\StatusValueData;
use Tuleap\ScaledAgile\Program\Backlog\ProgramIncrement\Source\Changeset\Values\TitleValueData;
use Tuleap\ScaledAgile\Program\Backlog\ProgramIncrement\Source\Fields\FieldData;
use Tuleap\ScaledAgile\Program\Backlog\ProgramIncrement\Source\Fields\NoDuckTypedMatchingValueException;
use Tuleap\Tracker\FormElement\Field\ListFields\FieldValueMatcher;

final class StatusValueMapperTest extends \PHPUnit\Framework\TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var StatusValueMapper
     */
    private $mapper;
    /**
     * @var M\LegacyMockInterface|M\MockInterface|FieldValueMatcher
     */
    private $matcher;

    protected function setUp(): void
    {
        $this->matcher = M::mock(FieldValueMatcher::class);
        $this->mapper = new StatusValueMapper($this->matcher);
    }

    public function testItMapsValuesByDuckTyping(): void
    {
        $first_list_value  = new \Tracker_FormElement_Field_List_Bind_StaticValue(2000, 'Not found', 'Irrelevant', 1, 0);
        $second_list_value = new \Tracker_FormElement_Field_List_Bind_StaticValue(2001, 'Planned', 'Irrelevant', 2, 0);
        $copied_values     = $this->buildCopiedValues([$first_list_value, $second_list_value]);
        $status_field_data = new FieldData(
            new \Tracker_FormElement_Field_Selectbox(1004, 89, 1000, 'status', 'Status', 'Irrelevant', true, 'P', false, '', 4)
        );

        $first_mapped_value  = new \Tracker_FormElement_Field_List_Bind_StaticValue(3000, 'Not found', 'Irrelevant', 1, 0);
        $second_mapped_value = new \Tracker_FormElement_Field_List_Bind_StaticValue(3001, 'Planned', 'Irrelevant', 2, 0);
        $this->matcher->shouldReceive('getMatchingBindValueByDuckTyping')
            ->once()
            ->with($first_list_value, M::type(\Tracker_FormElement_Field_List::class))
            ->andReturn($first_mapped_value);
        $this->matcher->shouldReceive('getMatchingBindValueByDuckTyping')
            ->once()
            ->with($second_list_value, M::type(\Tracker_FormElement_Field_List::class))
            ->andReturn($second_mapped_value);

        $result        = $this->mapper->mapStatusValueByDuckTyping($copied_values, $status_field_data);
        $mapped_values = $result->getValues();
        self::assertContains(3000, $mapped_values);
        self::assertContains(3001, $mapped_values);
    }

    public function testItThrowsWhenOneValueCannotBeMapped(): void
    {
        $first_list_value  = new \Tracker_FormElement_Field_List_Bind_StaticValue(2000, 'Not found', 'Irrelevant', 1, 0);
        $second_list_value = new \Tracker_FormElement_Field_List_Bind_StaticValue(2001, 'Planned', 'Irrelevant', 1, 0);
        $copied_values     = $this->buildCopiedValues([$first_list_value, $second_list_value]);
        $status_field_data = new FieldData(new \Tracker_FormElement_Field_Selectbox(
            1004,
            89,
            1000,
            'status',
            'Status',
            'Irrelevant',
            true,
            'P',
            false,
            '',
            4
        ));
        $this->matcher->shouldReceive('getMatchingBindValueByDuckTyping')
            ->andReturnNull();

        $this->expectException(NoDuckTypedMatchingValueException::class);
        $this->mapper->mapStatusValueByDuckTyping($copied_values, $status_field_data);
    }

    /**
     * @param Tracker_FormElement_Field_List_Bind_StaticValue[]  $status_value
     */
    private function buildCopiedValues(
        array $status_value
    ): SourceChangesetValuesCollection {
        $title_value         = new TitleValueData('Irrelevant');
        $description_value   = new DescriptionValueData('Irrelevant', 'text');
        $status_value        = new StatusValueData($status_value);
        $start_date_value    = new StartDateValueData('2020-10-01');
        $end_period_value    = new EndPeriodValueData('2020-10-10');
        $artifact_link_value = new ArtifactLinkValueData(123);

        return new SourceChangesetValuesCollection(
            123,
            $title_value,
            $description_value,
            $status_value,
            123456789,
            $start_date_value,
            $end_period_value,
            $artifact_link_value
        );
    }
}
