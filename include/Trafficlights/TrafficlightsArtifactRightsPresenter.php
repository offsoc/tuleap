<?php

/**
 * Copyright (c) Enalean, 2016-2017. All Rights Reserved.
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
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
namespace Tuleap\TestManagement;

use Tuleap\RealTime\MessageRightsPresenter;
use Tracker_Permission_PermissionsSerializer;
use Tracker_Artifact;

class TrafficlightsArtifactRightsPresenter implements MessageRightsPresenter
{
    public $submitter_id;
    public $submitter_can_view;
    public $submitter_only;
    public $artifact;
    public $tracker;
    public $field;

    public function __construct(
        Tracker_Artifact $artifact,
        Tracker_Permission_PermissionsSerializer $permission_serializer
    ) {
        $this->submitter_id       = intval($artifact->getSubmittedByUser()->getId());
        $this->submitter_can_view = $artifact->userCanView($artifact->getSubmittedByUser());
        $this->submitter_only     = $permission_serializer->getLiteralizedUserGroupsSubmitterOnly($artifact);
        $this->artifact           = $permission_serializer->getLiteralizedUserGroupsThatCanViewArtifact($artifact);
        $this->tracker            = $permission_serializer->getLiteralizedUserGroupsThatCanViewTracker($artifact);
        $this->field              = $permission_serializer->getLiteralizedUserGroupsThatCanViewTrackerFields($artifact);
    }
}