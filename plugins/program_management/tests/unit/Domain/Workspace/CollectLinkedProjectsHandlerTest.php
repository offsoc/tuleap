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

namespace Tuleap\ProgramManagement\Domain\Workspace;

use Tuleap\ProgramManagement\Adapter\Events\CollectLinkedProjectsProxy;
use Tuleap\ProgramManagement\Adapter\Workspace\ProgramsSearcher;
use Tuleap\ProgramManagement\Adapter\Workspace\TeamsSearcher;
use Tuleap\ProgramManagement\Tests\Stub\RetrieveFullProjectStub;
use Tuleap\ProgramManagement\Tests\Stub\SearchProgramsOfTeamStub;
use Tuleap\ProgramManagement\Tests\Stub\SearchTeamsOfProgramStub;
use Tuleap\ProgramManagement\Tests\Stub\VerifyIsProgramStub;
use Tuleap\ProgramManagement\Tests\Stub\VerifyIsTeamStub;
use Tuleap\Project\Sidebar\CollectLinkedProjects;
use Tuleap\Test\Builders\ProjectTestBuilder;
use Tuleap\Test\Builders\UserTestBuilder;
use Tuleap\Test\PHPUnit\TestCase;
use Tuleap\Test\Stubs\CheckProjectAccessStub;

final class CollectLinkedProjectsHandlerTest extends TestCase
{
    private CollectLinkedProjects $original_event;
    private CheckProjectAccessStub $access_checker;

    protected function setUp(): void
    {
        $source_project       = ProjectTestBuilder::aProject()->build();
        $this->original_event = new CollectLinkedProjects($source_project, UserTestBuilder::aUser()->build());
        $this->access_checker = CheckProjectAccessStub::withValidAccess();
    }

    public function testItBuildsACollectionOfTeamProjects(): void
    {
        $handler = new CollectLinkedProjectsHandler(
            VerifyIsProgramStub::withValidProgram(),
            VerifyIsTeamStub::withNotValidTeam()
        );

        $event = $this->buildEventForProgram();
        $handler->handle($event);

        $collection = $this->original_event->getChildrenProjects();
        self::assertFalse($collection->isEmpty());
        self::assertCount(2, $collection->getProjects());
    }

    public function testItBuildsACollectionOfProgramProjects(): void
    {
        $handler = new CollectLinkedProjectsHandler(
            VerifyIsProgramStub::withNotValidProgram(),
            VerifyIsTeamStub::withValidTeam()
        );

        $event = $this->buildEventForTeam();
        $handler->handle($event);

        $collection = $this->original_event->getParentProjects();
        self::assertFalse($collection->isEmpty());
        self::assertCount(2, $collection->getProjects());
    }

    public function testDoesNothingWhenProjectIsNotAProgramAndNotATeam(): void
    {
        $handler = new CollectLinkedProjectsHandler(
            VerifyIsProgramStub::withNotValidProgram(),
            VerifyIsTeamStub::withNotValidTeam()
        );

        $event = $this->buildEventForTeam();
        $handler->handle($event);

        $collection = $this->original_event->getParentProjects();
        self::assertTrue($collection->isEmpty());
        self::assertEmpty($collection->getProjects());

        $collection = $this->original_event->getChildrenProjects();
        self::assertTrue($collection->isEmpty());
        self::assertEmpty($collection->getProjects());
    }

    private function buildEventForProgram(): CollectLinkedProjectsProxy
    {
        $red_team              = ProjectTestBuilder::aProject()->build();
        $blue_team             = ProjectTestBuilder::aProject()->build();
        $retrieve_full_project = RetrieveFullProjectStub::withSuccessiveProjects($red_team, $blue_team);
        $teams_searcher        = new TeamsSearcher(
            SearchTeamsOfProgramStub::withTeamIds(103, 104),
            $retrieve_full_project
        );

        $retrieve_full_project = RetrieveFullProjectStub::withoutProject();
        $programs_searcher     = new ProgramsSearcher(
            SearchProgramsOfTeamStub::withNoPrograms(),
            $retrieve_full_project
        );

        return CollectLinkedProjectsProxy::fromCollectLinkedProjects(
            $teams_searcher,
            $this->access_checker,
            $programs_searcher,
            $this->original_event
        );
    }

    private function buildEventForTeam(): CollectLinkedProjectsProxy
    {
        $red_program  = ProjectTestBuilder::aProject()->build();
        $blue_program = ProjectTestBuilder::aProject()->build();

        $retrieve_full_project = RetrieveFullProjectStub::withoutProject();
        $teams_searcher        = new TeamsSearcher(
            SearchTeamsOfProgramStub::withNoTeams(),
            $retrieve_full_project
        );

        $retrieve_full_project = RetrieveFullProjectStub::withSuccessiveProjects($red_program, $blue_program);

        $programs_searcher = new ProgramsSearcher(
            SearchProgramsOfTeamStub::buildPrograms(110, 111),
            $retrieve_full_project
        );

        return CollectLinkedProjectsProxy::fromCollectLinkedProjects(
            $teams_searcher,
            $this->access_checker,
            $programs_searcher,
            $this->original_event
        );
    }
}
