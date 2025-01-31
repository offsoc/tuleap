<?php
/**
 * Copyright (c) Enalean, 2024 - Present. All Rights Reserved.
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

namespace Tuleap\Timetracking\REST\v1\TimetrackingManagement;

use Tuleap\NeverThrow\Err;
use Tuleap\NeverThrow\Fault;
use Tuleap\NeverThrow\Ok;
use Tuleap\NeverThrow\Result;
use Tuleap\Test\PHPUnit\TestCase;
use Tuleap\Timetracking\Tests\Stub\SaveQueryStub;

final class QueryPUTHandlerTest extends TestCase
{
    /**
     * @return Ok<true> | Err<Fault>
     */
    public function handle(string $start_date, string $end_date): Ok|Err
    {
        $widget_id      = 10;
        $representation = (new QueryPUTRepresentation(
            $start_date,
            $end_date,
        ));

        $handler = new QueryPUTHandler(
            new QueryTimePeriodChecker(),
            new TimetrackingManagementWidgetSaver(SaveQueryStub::build()),
        );
        return $handler->handle($widget_id, $representation);
    }

    public function testUpdateQuery(): void
    {
        $result = $this->handle('2024-06-27T15:46:00z', '2024-06-27T15:46:00z');

        self::assertTrue(Result::isOk($result));
        self::assertTrue($result->value);
    }

    public function testFaultWhenInvalidDateFormat(): void
    {
        $result = $this->handle('hello', '2024-06-27T15:46:00z');

        self::assertTrue(Result::isErr($result));
        self::assertInstanceOf(QueryInvalidDateFormatFault::class, $result->error);
    }

    public function testFaultEndDateLesserThanStartDate(): void
    {
        $result = $this->handle('2024-06-27T15:46:00z', '2023-05-26T15:46:00z');

        self::assertTrue(Result::isErr($result));
        self::assertInstanceOf(QueryEndDateLesserThanStartDateFault::class, $result->error);
    }
}
