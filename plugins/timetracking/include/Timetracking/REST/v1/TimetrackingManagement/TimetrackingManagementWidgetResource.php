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

use Luracast\Restler\RestException;
use Tuleap\REST\AuthenticatedResource;
use Tuleap\REST\Header;

final class TimetrackingManagementWidgetResource extends AuthenticatedResource
{
    public const NAME = 'timetracking_management_widget';

    /**
     * @url OPTIONS {id}/query
     */
    public function allowQuery(int $id): void
    {
        Header::allowOptionsPut();
    }

    private function getPUTHandler(): QueryPUTHandler
    {
        return (new QueryPUTHandler(
            new QueryTimePeriodChecker(),
            new TimetrackingManagementWidgetSaver(new Dao())
        ));
    }

    /**
     * Update a query
     *
     * Update the query of a given Timetracking Management widget.<br>
     *
     * <br>
     * With dates:
     * <br>
     * <pre>
     * {<br>
     * &nbsp;"start_date": "2024-06-06T00:00:00z",<br>
     * &nbsp;"end_date": "2024-06-06T00:00:00z"<br>
     * }
     * </pre>
     *
     *
     * @url PUT {id}/query
     * @status 200
     * @param int $id Id of the timetracking management widget
     * @param QueryPUTRepresentation $item The edited query
     *
     *
     * @throws RestException
     */
    protected function putQuery(int $id, QueryPUTRepresentation $item): void
    {
        $this->checkAccess();

        Header::allowOptionsPut();

        $this->getPUTHandler()
            ->handle($id, $item)
            ->mapErr(FaultMapper::mapToRestException(...));
    }
}
