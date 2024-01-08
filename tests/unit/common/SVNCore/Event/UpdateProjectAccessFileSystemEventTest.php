<?php
/**
 * Copyright (c) Enalean, 2020-Present. All Rights Reserved.
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

namespace Tuleap\SVNCore\Event;

use Psr\EventDispatcher\EventDispatcherInterface;

final class UpdateProjectAccessFileSystemEventTest extends \Tuleap\Test\PHPUnit\TestCase
{
    private const PROJECT_ID = 102;

    /**
     * @var \BackendSVN|\Mockery\LegacyMockInterface|\Mockery\MockInterface
     */
    private $backend_svn;
    /**
     * @var \Mockery\LegacyMockInterface|\Mockery\MockInterface|\ProjectManager
     */
    private $project_manager;
    /**
     * @var \Mockery\LegacyMockInterface|\Mockery\MockInterface|EventDispatcherInterface
     */
    private $event_dispatcher;

    /**
     * @var UpdateProjectAccessFileSystemEvent
     */
    private $system_event;

    protected function setUp(): void
    {
        $this->backend_svn = $this->createMock(\BackendSVN::class);
        \Backend::setInstance(\Backend::SVN, $this->backend_svn);
        $this->project_manager  = $this->createMock(\ProjectManager::class);
        $this->event_dispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->system_event = new UpdateProjectAccessFileSystemEvent(
            12,
            \SystemEvent::TYPE_SVN_UPDATE_PROJECT_ACCESS_FILES,
            \SystemEvent::OWNER_ROOT,
            (string) self::PROJECT_ID,
            \SystemEvent::PRIORITY_MEDIUM,
            \SystemEvent::STATUS_NEW,
            10,
            0,
            0,
            '',
        );
        $this->system_event->injectDependencies($this->project_manager, $this->event_dispatcher);
    }

    protected function tearDown(): void
    {
        \Backend::clearInstances();
    }

    public function testCanProcessAccessFilesChanges(): void
    {
        $project = $this->createMock(\Project::class);
        $this->project_manager->method('getProject')->with(self::PROJECT_ID)->willReturn($project);

        $this->event_dispatcher->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(UpdateProjectAccessFilesEvent::class));

        $this->system_event->process();
    }
}
