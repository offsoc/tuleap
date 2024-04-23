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

use ParagonIE\EasyDB\EasyStatement;
use Tracker;
use Tuleap\DB\DataAccessObject;
use Tuleap\Tracker\Artifact\Artifact;

final class TrackersPermissionsDao extends DataAccessObject implements SearchUserGroupsPermissionOnFields, SearchUserGroupsPermissionOnTrackers, SearchUserGroupsPermissionOnArtifacts
{
    public function searchUserGroupsPermissionOnFields(array $user_groups_id, array $fields_id, string $permission): array
    {
        $ugroups_statement = EasyStatement::open()->in('permissions.ugroup_id IN (?*)', $user_groups_id);
        $fields_statement  = EasyStatement::open()->in('permissions.object_id IN (?*)', $fields_id);

        $sql = <<<SQL
        SELECT DISTINCT object_id AS field_id
        FROM permissions
        WHERE $ugroups_statement AND $fields_statement AND permissions.permission_type = ?
        SQL;

        $results = $this->getDB()->safeQuery($sql, [...$user_groups_id, ...$this->castIdsToString($fields_id), $permission]);
        assert(is_array($results));
        return array_map(static fn(array $row) => (int) $row['field_id'], $results);
    }

    public function searchUserGroupsViewPermissionOnTrackers(array $user_groups_id, array $trackers_id): array
    {
        $ugroups_statement   = EasyStatement::open()->in('permissions.ugroup_id IN (?*)', $user_groups_id);
        $trackers_statement  = EasyStatement::open()->in('tracker.id IN (?*)', $trackers_id);
        $perm_type_statement = EasyStatement::open()->in('permissions.permission_type IN (?*)', [
            Tracker::PERMISSION_ADMIN,
            Tracker::PERMISSION_FULL,
            Tracker::PERMISSION_ASSIGNEE,
            Tracker::PERMISSION_SUBMITTER,
            Tracker::PERMISSION_SUBMITTER_ONLY,
        ]);

        $sql = <<<SQL
        SELECT DISTINCT tracker.id AS tracker_id
        FROM tracker
        INNER JOIN permissions ON (
            permissions.object_id = CAST(tracker.id AS CHAR CHARACTER SET utf8)
            AND $ugroups_statement
            AND $perm_type_statement
        )
        WHERE tracker.deletion_date IS NULL AND $trackers_statement
        SQL;

        $results = $this->getDB()->safeQuery($sql, [
            ...$user_groups_id,
            ...array_values($perm_type_statement->values()),
            ...$trackers_id,
        ]);
        assert(is_array($results));
        return array_map(static fn(array $row) => (int) $row['tracker_id'], $results);
    }

    public function searchUserGroupsSubmitPermissionOnTrackers(array $user_groups_id, array $trackers_id): array
    {
        $ugroups_tracker_statement = EasyStatement::open()->in('tracker_permission.ugroup_id IN (?*)', $user_groups_id);
        $ugroups_field_statement   = EasyStatement::open()->in('field_permission.ugroup_id IN (?*)', $user_groups_id);
        $trackers_statement        = EasyStatement::open()->in('tracker.id IN (?*)', $trackers_id);

        $sql = <<<SQL
        SELECT DISTINCT tracker.id AS tracker_id
        FROM permissions AS tracker_permission
        INNER JOIN tracker ON (tracker_permission.object_id = CAST(tracker.id AS CHAR CHARACTER SET utf8) AND tracker.deletion_date IS NULL)
        INNER JOIN tracker_field AS field ON (tracker.id = field.tracker_id)
        INNER JOIN permissions AS field_permission ON (
            field_permission.object_id = CAST(field.id AS CHAR CHARACTER SET utf8) AND field_permission.permission_type = ?
        )
        WHERE $ugroups_tracker_statement AND $ugroups_field_statement AND $trackers_statement AND tracker_permission.permission_type <> ?
        SQL;

        $results = $this->getDB()->safeQuery($sql, [
            FieldPermissionType::PERMISSION_SUBMIT->value,
            ...$user_groups_id,
            ...$user_groups_id,
            ...$trackers_id,
            Tracker::PERMISSION_NONE,
        ]);
        assert(is_array($results));
        return array_map(static fn(array $row) => (int) $row['tracker_id'], $results);
    }

    public function searchUserGroupsViewPermissionOnArtifacts(array $user_groups_id, array $artifacts_id): array
    {
        $artifacts_statement       = EasyStatement::open()->in('artifact.id IN (?*)', $artifacts_id);
        $ugroup_tracker_statement  = EasyStatement::open()->in('tracker_permission.ugroup_id IN (?*)', $user_groups_id);
        $ugroup_artifact_statement = EasyStatement::open()->in('artifact_permission.ugroup_id IN (?*)', $user_groups_id);

        $sql = <<<SQL
        SELECT DISTINCT artifact.id AS artifact_id
        FROM tracker_artifact AS artifact
        INNER JOIN tracker ON (artifact.tracker_id = tracker.id AND tracker.deletion_date IS NULL)
        INNER JOIN permissions AS tracker_permission ON (
            tracker_permission.object_id = CAST(tracker.id AS CHAR CHARACTER SET utf8)
            AND $ugroup_tracker_statement
        )
        LEFT JOIN permissions AS artifact_permission ON (
            artifact.use_artifact_permissions = 1
            AND artifact_permission.object_id = CAST(artifact.id AS CHAR CHARACTER SET utf8)
            AND artifact_permission.permission_type = ?
            AND $ugroup_artifact_statement
        )
        WHERE $artifacts_statement AND (
            artifact.use_artifact_permissions = 0 OR (
                artifact.use_artifact_permissions = 1 AND artifact_permission.object_id IS NOT NULL
            )
        )
        SQL;

        $results = $this->getDB()->safeQuery($sql, [
            ...$user_groups_id,
            Artifact::PERMISSION_ACCESS,
            ...$user_groups_id,
            ...$artifacts_id,
        ]);
        assert(is_array($results));
        return array_map(static fn(array $row) => (int) $row['artifact_id'], $results);
    }

    /**
     * @param int[] $ids
     * @return string[]
     */
    private function castIdsToString(array $ids): array
    {
        return array_map(static fn(int $id) => (string) $id, $ids);
    }
}
