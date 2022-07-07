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

namespace Tuleap\Gitlab\Repository\Webhook\PostPush;

use Tuleap\Gitlab\Reference\Commit\GitlabCommitReference;
use Tuleap\Gitlab\Repository\GitlabRepositoryIntegration;
use Tuleap\Gitlab\Repository\Webhook\WebhookTuleapReference;
use Tuleap\Gitlab\Repository\Webhook\WebhookTuleapReferencesParser;
use Tuleap\Tracker\Artifact\Artifact;
use Tuleap\Tracker\Artifact\Closure\ArtifactClosingCommentInCommonMarkFormat;

/**
 * @psalm-immutable
 */
final class PostPushArtifactComment implements ArtifactClosingCommentInCommonMarkFormat
{
    private function __construct(private string $comment)
    {
    }

    public static function fromCommit(
        string $user_name,
        PostPushCommitWebhookData $commit,
        WebhookTuleapReference $tuleap_reference,
        GitlabRepositoryIntegration $gitlab_repository_integration,
        Artifact $artifact,
    ): self {
        if (
            $tuleap_reference->getCloseArtifactKeyword() !== WebhookTuleapReferencesParser::RESOLVES_KEYWORD &&
            $tuleap_reference->getCloseArtifactKeyword() !== WebhookTuleapReferencesParser::CLOSES_KEYWORD &&
            $tuleap_reference->getCloseArtifactKeyword() !== WebhookTuleapReferencesParser::FIXES_KEYWORD &&
            $tuleap_reference->getCloseArtifactKeyword() !== WebhookTuleapReferencesParser::IMPLEMENTS_KEYWORD
        ) {
            return new self('');
        }

        $action_word = "solved";
        if ($tuleap_reference->getCloseArtifactKeyword() === WebhookTuleapReferencesParser::CLOSES_KEYWORD) {
            $action_word = "closed";
        } elseif ($tuleap_reference->getCloseArtifactKeyword() === WebhookTuleapReferencesParser::FIXES_KEYWORD) {
            $action_word = "{$artifact->getTracker()->getItemName()} fixed";
        } elseif ($tuleap_reference->getCloseArtifactKeyword() === WebhookTuleapReferencesParser::IMPLEMENTS_KEYWORD) {
            $action_word = "implemented";
        }

        return new self(
            "$action_word by $user_name with " . GitlabCommitReference::REFERENCE_NAME . " #{$gitlab_repository_integration->getName()}/{$commit->getSha1()}"
        );
    }

    public function getBody(): string
    {
        return $this->comment;
    }
}
