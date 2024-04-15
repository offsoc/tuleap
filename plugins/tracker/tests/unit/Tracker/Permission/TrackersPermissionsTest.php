<?php
/**
 * Copyright (c) Enalean, 2024-Present. All Rights Reserved.
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

namespace Tuleap\Tracker\Permission;

use ForgeConfig;
use Tuleap\ForgeConfigSandbox;
use Tuleap\Test\PHPUnit\TestCase;

final class TrackersPermissionsTest extends TestCase
{
    use ForgeConfigSandbox;

    private TrackersPermissions $permissions;

    protected function setUp(): void
    {
        $this->permissions = new TrackersPermissions();
    }

    public function testIsEnabled(): void
    {
        ForgeConfig::setFeatureFlag(TrackersPermissions::FEATURE_FLAG, 0);
        self::assertFalse($this->permissions->isEnabled());
        ForgeConfig::setFeatureFlag(TrackersPermissions::FEATURE_FLAG, 1);
        self::assertTrue($this->permissions->isEnabled());
    }
}
