<?php
/**
 * Copyright (c) Enalean, 2017 - Present. All Rights Reserved.
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

namespace Tuleap\Trove;

class TroveCatCategoryPresenter
{
    public $id;
    public $count;
    public $name;
    public $none = false;
    public $selected;
    public $has_projects;

    public function __construct($id, $name, $count, $selected)
    {
        $this->id       = $id;
        $this->name     = $name;
        $this->count    = $count;
        $this->selected = $selected;

        $this->has_projects = $count > 0;
    }
}
