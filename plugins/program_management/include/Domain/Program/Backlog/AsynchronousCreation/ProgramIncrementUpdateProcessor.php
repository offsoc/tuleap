<?php
/**
 * Copyright (c) Enalean, 2021-Present. All Rights Reserved.
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

namespace Tuleap\ProgramManagement\Domain\Program\Backlog\AsynchronousCreation;

use Psr\Log\LoggerInterface;
use Tuleap\ProgramManagement\Domain\Program\Backlog\ProgramIncrement\ProgramIncrementUpdate;
use Tuleap\ProgramManagement\Domain\Program\Backlog\ProgramIncrement\Source\Changeset\RetrieveChangesetSubmissionDate;
use Tuleap\ProgramManagement\Domain\Program\Backlog\ProgramIncrement\Source\Changeset\Values\RetrieveFieldValuesGatherer;
use Tuleap\ProgramManagement\Domain\Program\Backlog\ProgramIncrement\Source\Changeset\Values\SourceTimeboxChangesetValues;
use Tuleap\ProgramManagement\Domain\Program\Backlog\ProgramIncrement\Source\Fields\GatherSynchronizedFields;
use Tuleap\ProgramManagement\Domain\Program\Backlog\ProgramIncrement\Source\Fields\SynchronizedFieldReferences;
use Tuleap\ProgramManagement\Domain\Team\MirroredTimebox\SearchMirroredTimeboxes;
use Tuleap\ProgramManagement\Domain\Workspace\RetrieveTrackerOfArtifact;

final class ProgramIncrementUpdateProcessor implements ProcessProgramIncrementUpdate
{
    public function __construct(
        private LoggerInterface $logger,
        private GatherSynchronizedFields $fields_gatherer,
        private RetrieveFieldValuesGatherer $values_retriever,
        private RetrieveChangesetSubmissionDate $submission_date_retriever,
        private SearchMirroredTimeboxes $mirrored_timeboxes_searcher,
        private RetrieveTrackerOfArtifact $tracker_retriever
    ) {
    }

    public function processProgramIncrementUpdate(ProgramIncrementUpdate $update): void
    {
        $program_increment_id = $update->program_increment->getId();
        $user_id              = $update->user->getId();
        $this->logger->debug(
            "Processing program increment update with program increment #$program_increment_id for user #$user_id"
        );

        $source_values = SourceTimeboxChangesetValues::fromUpdate(
            $this->fields_gatherer,
            $this->values_retriever,
            $this->submission_date_retriever,
            $update
        );

        $mirrored_program_increments = $this->mirrored_timeboxes_searcher->searchMirroredTimeboxes(
            $program_increment_id
        );

        foreach ($mirrored_program_increments as $mirrored_program_increment) {
            $mirror_tracker = $this->tracker_retriever->getTrackerOfArtifact($mirrored_program_increment->getId());
            $target_fields  = SynchronizedFieldReferences::fromTrackerIdentifier(
                $this->fields_gatherer,
                $mirror_tracker,
                null
            );
            $this->logger->debug(sprintf('Mirror PI title field id: %s', $target_fields->title->getId()));
        }

        $this->logger->debug(sprintf('Title value: %s', $source_values->getTitleValue()->getValue()));
    }
}
