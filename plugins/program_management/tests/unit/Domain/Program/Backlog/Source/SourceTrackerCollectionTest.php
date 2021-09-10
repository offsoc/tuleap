<?php
/**
 * Copyright (c) Enalean, 2021 - Present. All Rights Reserved.
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

namespace Tuleap\ProgramManagement\Domain\Program\Backlog\Source;

use Tuleap\ProgramManagement\Domain\Program\Backlog\ProgramIncrement\Team\TeamProjectsCollection;
use Tuleap\ProgramManagement\Domain\Program\Backlog\TrackerCollection;
use Tuleap\ProgramManagement\Domain\Program\ProgramIdentifier;
use Tuleap\ProgramManagement\Domain\ProgramTracker;
use Tuleap\ProgramManagement\Domain\Workspace\UserIdentifier;
use Tuleap\ProgramManagement\Tests\Builder\ProgramIdentifierBuilder;
use Tuleap\ProgramManagement\Tests\Builder\ProgramTrackerBuilder;
use Tuleap\ProgramManagement\Tests\Stub\BuildProjectStub;
use Tuleap\ProgramManagement\Tests\Stub\SearchTeamsOfProgramStub;
use Tuleap\ProgramManagement\Tests\Stub\RetrievePlanningMilestoneTrackerStub;
use Tuleap\ProgramManagement\Tests\Stub\RetrieveVisibleIterationTrackerStub;
use Tuleap\ProgramManagement\Tests\Stub\RetrieveVisibleProgramIncrementTrackerStub;
use Tuleap\ProgramManagement\Tests\Stub\UserIdentifierStub;
use Tuleap\Tracker\Test\Builders\TrackerTestBuilder;

final class SourceTrackerCollectionTest extends \Tuleap\Test\PHPUnit\TestCase
{
    private TeamProjectsCollection $teams;
    private UserIdentifier $user;
    private ProgramIdentifier $program;
    private \Tracker $timebox_tracker;
    private \Tracker $blue_team_tracker;
    private \Tracker $red_team_tracker;
    private TrackerCollection $team_trackers;
    private ProgramTracker $timebox_program_tracker;

    protected function setUp(): void
    {
        $this->user    = UserIdentifierStub::buildGenericUser();
        $this->program = ProgramIdentifierBuilder::build();

        $this->teams = TeamProjectsCollection::fromProgramIdentifier(
            SearchTeamsOfProgramStub::buildTeams(102, 103),
            new BuildProjectStub(),
            $this->program
        );

        $this->timebox_program_tracker = ProgramTrackerBuilder::buildWithId(78);
        $this->timebox_tracker         = TrackerTestBuilder::aTracker()->withId(78)->build();
        $this->blue_team_tracker       = TrackerTestBuilder::aTracker()->withId(79)->build();
        $this->red_team_tracker        = TrackerTestBuilder::aTracker()->withId(80)->build();

        $this->team_trackers = TrackerCollection::buildRootPlanningMilestoneTrackers(
            RetrievePlanningMilestoneTrackerStub::withValidTrackers(ProgramTrackerBuilder::buildWithTracker($this->blue_team_tracker), ProgramTrackerBuilder::buildWithTracker($this->red_team_tracker)),
            $this->teams,
            $this->user
        );
    }

    public function testItBuildsValidCollectionFromProgramIncrement(): void
    {
        $collection = SourceTrackerCollection::fromProgramAndTeamTrackers(
            RetrieveVisibleProgramIncrementTrackerStub::withValidTracker($this->timebox_program_tracker),
            $this->program,
            $this->team_trackers,
            $this->user
        );
        $trackers   = $collection->getSourceTrackers();
        self::assertContainsEquals($this->timebox_program_tracker, $trackers);
        self::assertContainsEquals(new ProgramTracker($this->blue_team_tracker), $trackers);
        self::assertContainsEquals(new ProgramTracker($this->red_team_tracker), $trackers);
    }

    public function testItBuildsValidCollectionFromIteration(): void
    {
        $collection = SourceTrackerCollection::fromIterationAndTeamTrackers(
            RetrieveVisibleIterationTrackerStub::withValidTracker($this->timebox_tracker),
            $this->program,
            $this->team_trackers,
            $this->user
        );

        $trackers = $collection->getSourceTrackers();
        self::assertContainsEquals(new ProgramTracker($this->timebox_tracker), $trackers);
        self::assertContainsEquals(new ProgramTracker($this->blue_team_tracker), $trackers);
        self::assertContainsEquals(new ProgramTracker($this->red_team_tracker), $trackers);
    }

    public function testItBuildsNullCollectionFromIteration(): void
    {
        $collection = SourceTrackerCollection::fromIterationAndTeamTrackers(
            RetrieveVisibleIterationTrackerStub::withNotVisibleIterationTracker(),
            $this->program,
            $this->team_trackers,
            $this->user
        );

        self::assertNull($collection);
    }
}
