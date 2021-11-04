<?php
/**
 * Copyright (c) Enalean 2021 -  Present. All Rights Reserved.
 *
 *  This file is a part of Tuleap.
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
 *
 */

declare(strict_types=1);

namespace Tuleap\ProgramManagement\Adapter\Program\Backlog\Timebox;

use Tuleap\ProgramManagement\Adapter\Workspace\RetrieveUser;
use Tuleap\ProgramManagement\Domain\Program\Backlog\Timebox\RetrieveUserCanUpdate;
use Tuleap\ProgramManagement\Domain\Program\Backlog\Timebox\TimeboxNotFoundException;
use Tuleap\ProgramManagement\Domain\Program\Backlog\TimeboxIdentifier;
use Tuleap\ProgramManagement\Domain\Workspace\UserIdentifier;

final class UserCanUpdateRetriever implements RetrieveUserCanUpdate
{

    public function __construct(private \Tracker_ArtifactFactory $artifact_factory, private RetrieveUser $retrieve_user)
    {
    }

    public function userCanUpdate(TimeboxIdentifier $timebox_identifier, UserIdentifier $user_identifier): bool
    {
        $artifact = $this->artifact_factory->getArtifactById($timebox_identifier->getId());
        if (! $artifact) {
            throw new TimeboxNotFoundException($timebox_identifier);
        }

        $user = $this->retrieve_user->getUserWithId($user_identifier);
        return $artifact->userCanUpdate($user);
    }
}
