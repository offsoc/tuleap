<?php
/**
 * Copyright (c) Enalean, 2019. All Rights Reserved.
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

namespace Tuleap\Baseline;

use DateTime;
use PFUser;
use Tracker_Artifact;

class Baseline extends TransientBaseline
{
    /** @var int */
    private $id;

    /** @var DateTime */
    private $snapshot_date;

    /** @var PFUser */
    private $author;

    public function __construct(
        int $id,
        string $name,
        Tracker_Artifact $milestone,
        DateTime $snapshot_date,
        PFUser $author
    ) {
        parent::__construct($name, $milestone);
        $this->id            = $id;
        $this->snapshot_date = $snapshot_date;
        $this->author        = $author;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSnapshotDate(): DateTime
    {
        return $this->snapshot_date;
    }

    public function getAuthor(): PFUser
    {
        return $this->author;
    }
}
