<?php
/**
 * Copyright (c) Enalean, 2018. All Rights Reserved.
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

namespace Tuleap\TestManagement\Campaign\AutomatedTests;

use Jenkins_Client;
use Tuleap\TestManagement\Campaign\Campaign;
use Tuleap\TestManagement\Campaign\CampaignDao;

class AutomatedTestsTriggerer
{
    /** @var Jenkins_Client */
    private $jenkins_client;

    public function __construct(Jenkins_Client $jenkins_client)
    {
        $this->jenkins_client = $jenkins_client;
    }

    /**
     * @param Campaign $campaign
     *
     * @throws NoJobConfiguredForCampaignException
     * @throws \Jenkins_ClientUnableToLaunchBuildException
     */
    public function triggerAutomatedTests(Campaign $campaign)
    {
        $job = $campaign->getJobConfiguration();
        $url = $job->getUrl();
        if (! $url) {
            throw new NoJobConfiguredForCampaignException();
        }

        $this->jenkins_client->setToken('yolo');

        $this->jenkins_client->launchJobBuild(
            $url,
            [
                'campaign' => $campaign->getLabel()
            ]
        );
    }
}
