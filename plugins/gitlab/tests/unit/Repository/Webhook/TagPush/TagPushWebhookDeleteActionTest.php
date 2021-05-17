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
 * along with Tuleap. If not, see http://www.gnu.org/licenses/.
 */

declare(strict_types=1);

namespace Tuleap\Gitlab\Repository\Webhook\TagPush;

use CrossReferenceManager;
use DateTimeImmutable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Project;
use Psr\Log\NullLogger;
use Tuleap\Gitlab\Reference\Tag\GitlabTagReference;
use Tuleap\Gitlab\Repository\GitlabRepository;

class TagPushWebhookDeleteActionTest extends \Tuleap\Test\PHPUnit\TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var TagPushWebhookDeleteAction
     */
    private $delete_action;
    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface|TagInfoDao
     */
    private $tag_info_dao;
    /**
     * @var CrossReferenceManager|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private $cross_reference_manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tag_info_dao            = Mockery::mock(TagInfoDao::class);
        $this->cross_reference_manager = Mockery::mock(CrossReferenceManager::class);

        $this->delete_action = new TagPushWebhookDeleteAction(
            $this->tag_info_dao,
            $this->cross_reference_manager,
            new NullLogger(),
        );
    }

    public function testItDeletesTheTagReferencesAndInformation(): void
    {
        $gitlab_repository = new GitlabRepository(
            1,
            12587,
            "root/repo01",
            "",
            "https://example.com/root/repo01",
            new DateTimeImmutable(),
            Project::buildForTest(),
            false
        );

        $tag_webhook_data = new TagPushWebhookData(
            "Tag Push Event",
            12587,
            "https://example.com",
            "refs/tags/v1.0.2",
            "before",
            "0000000000000000000000000000000000000000",
        );

        $this->cross_reference_manager->shouldReceive('deleteEntity')
            ->with(
                "root/repo01/v1.0.2",
                GitlabTagReference::NATURE_NAME,
                101
            )
            ->once();

        $this->tag_info_dao->shouldReceive('deleteTagInGitlabRepository')
            ->once()
            ->with(
                1,
                "v1.0.2"
            );

        $this->delete_action->deleteTagReferences(
            $gitlab_repository,
            $tag_webhook_data
        );
    }
}
