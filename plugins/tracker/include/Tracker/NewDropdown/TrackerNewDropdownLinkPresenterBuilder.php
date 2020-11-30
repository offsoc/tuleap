<?php
/**
 * Copyright (c) Enalean, 2020 - Present. All Rights Reserved.
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

namespace Tuleap\Tracker\NewDropdown;

use Tuleap\layout\NewDropdown\DataAttributePresenter;
use Tuleap\layout\NewDropdown\NewDropdownLinkPresenter;

final class TrackerNewDropdownLinkPresenterBuilder
{
    public function build(\Tracker $tracker): NewDropdownLinkPresenter
    {
        return $this->buildWithAdditionalDataAttributes($tracker, []);
    }

    /**
     * @param DataAttributePresenter[] $data_attributes
     */
    public function buildWithAdditionalDataAttributes(\Tracker $tracker, array $data_attributes): NewDropdownLinkPresenter
    {
        return new NewDropdownLinkPresenter(
            $tracker->getSubmitUrl(),
            sprintf(
                dgettext('tuleap-tracker', 'New %s'),
                $tracker->getItemName()
            ),
            'fa-plus',
            array_merge(
                [new DataAttributePresenter('tracker-id', (string) $tracker->getId())],
                $data_attributes
            ),
        );
    }
}
