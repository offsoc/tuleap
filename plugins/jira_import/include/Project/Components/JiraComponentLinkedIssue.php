<?php
/**
 * Copyright (c) Enalean, 2023-Present. All Rights Reserved.
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
 *
 */

declare(strict_types=1);

namespace Tuleap\JiraImport\Project\Components;

/**
 * @psalm-immutable
 */
final class JiraComponentLinkedIssue
{
    private function __construct(
        public readonly int $id,
    ) {
    }

    /**
     * @throw ComponentAPIResponseNotWellFormedException
     */
    public static function buildFromAPIResponse(array $response): self
    {
        if (! isset($response['id'])) {
            throw new ComponentAPIResponseNotWellFormedException();
        }

        return new self(
            (int) $response['id'],
        );
    }

    public static function build(int $linked_issue_id): self
    {
        return new self($linked_issue_id);
    }
}
