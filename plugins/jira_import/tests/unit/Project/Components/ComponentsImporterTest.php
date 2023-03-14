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

use PFUser;
use Psr\Log\NullLogger;
use Tuleap\Test\Builders\UserTestBuilder;
use Tuleap\Test\PHPUnit\TestCase;
use Tuleap\Tracker\Creation\JiraImporter\Import\Structure\FieldAndValueIDGenerator;
use Tuleap\Tracker\Creation\JiraImporter\Import\User\GetTuleapUserFromJiraUser;
use Tuleap\Tracker\Creation\JiraImporter\Import\User\JiraUser;

final class ComponentsImporterTest extends TestCase
{
    public function testItDoesNothingIfNoComponentsInJiraProject(): void
    {
        $xml = $this->getXMLAfterExport($this->getComponentsImporterWithoutComponents());

        self::assertCount(0, $xml->trackers->tracker);
    }

    public function testItHasAComponentsTracker(): void
    {
        $xml = $this->getXMLAfterExport($this->getComponentsImporterWithComponents(
            [JiraComponent::build("Component 01", "", null)],
            [],
        ));

        self::assertCount(1, $xml->trackers->tracker);
        self::assertEquals('T1', (string) $xml->trackers->tracker[0]['id']);
        self::assertEquals('Components', (string) $xml->trackers->tracker[0]->name);
        self::assertEquals('components', (string) $xml->trackers->tracker[0]->item_name);
        self::assertEquals('acid-green', (string) $xml->trackers->tracker[0]->color);
    }

    public function testSprintTrackerHasNameForTitleSemantic(): void
    {
        $xml = $this->getXMLAfterExport($this->getComponentsImporterWithComponents(
            [JiraComponent::build("Component 01", "", null)],
            [],
        ));

        $name_field = $xml->xpath('/project/trackers/tracker/formElements//formElement[name="name"]');

        $title_semantic = $xml->xpath('/project/trackers/tracker/semantics/semantic[@type="title"]');
        self::assertCount(1, $title_semantic);
        self::assertEquals($name_field[0]['ID'], $title_semantic[0]->field['REF']);
    }

    public function testItCreatesOneComponentArtifact(): void
    {
        $jira_user = new /** @psalm-immutable */ class implements JiraUser {
            public function getDisplayName(): string
            {
                return '';
            }

            public function getEmailAddress(): string
            {
                return '';
            }

            public function getUniqueIdentifier(): string
            {
                return 'user01';
            }
        };

        $xml = $this->getXMLAfterExport($this->getComponentsImporterWithComponents(
            [JiraComponent::build("Component 01", "", $jira_user)],
            [JiraComponentLinkedIssue::build(10256)],
        ));

        self::assertCount(1, $xml->trackers->tracker[0]->artifacts->artifact);
        $xml_artifact_node = $xml->trackers->tracker[0]->artifacts->artifact[0];

        self::assertNotEmpty((string) $xml_artifact_node['id']);
        self::assertCount(1, $xml_artifact_node->changeset);
        self::assertEquals('username', $xml_artifact_node->changeset[0]->submitted_by['format']);
        self::assertEquals('forge__tracker_importer_user', $xml_artifact_node->changeset[0]->submitted_by);
        self::assertEquals('ISO8601', $xml_artifact_node->changeset[0]->submitted_on['format']);
        self::assertNotNull($xml_artifact_node->changeset[0]->submitted_on);
        self::assertNotNull($xml_artifact_node->changeset[0]->comments);
        self::assertCount(0, $xml_artifact_node->changeset[0]->comments->comment);

        $name_field_change = $xml_artifact_node->xpath('/project/trackers/tracker/artifacts/artifact/changeset/field_change[@field_name="name"]');
        self::assertCount(1, $name_field_change);
        self::assertEquals('string', $name_field_change[0]['type']);
        self::assertEquals('Component 01', $name_field_change[0]->value);

        $linked_issues_field_change = $xml_artifact_node->xpath('/project/trackers/tracker/artifacts/artifact/changeset/field_change[@field_name="linked_issues"]');
        self::assertCount(1, $linked_issues_field_change);
        self::assertEquals('art_link', $linked_issues_field_change[0]['type']);
        self::assertCount(1, $linked_issues_field_change[0]->value);
        self::assertEquals('10256', (string) $linked_issues_field_change[0]->value[0]);

        $linked_issues_field_change = $xml_artifact_node->xpath('/project/trackers/tracker/artifacts/artifact/changeset/field_change[@field_name="component_lead"]');
        self::assertCount(1, $linked_issues_field_change);
        self::assertEquals('list', (string) $linked_issues_field_change[0]['type']);
        self::assertCount(1, $linked_issues_field_change[0]->value);
        self::assertEquals('username', (string) $linked_issues_field_change[0]->value['format']);
        self::assertEquals('user01', (string) $linked_issues_field_change[0]->value);
    }

    private function getComponentsImporterWithoutComponents(): ComponentsImporter
    {
        return new ComponentsImporter(
            $this->getJiraComponentsRetrieverWithoutComponents(),
            $this->getJiraComponentLinkedIssuesRetrieverWithoutIssues(),
            new ComponentsTrackerBuilder(),
            $this->getJiraUserRetriever(),
            new NullLogger(),
        );
    }

    /**
     * @param JiraComponent[] $components
     * @param JiraComponentLinkedIssue[] $linked_issues
     */
    private function getComponentsImporterWithComponents(array $components, array $linked_issues): ComponentsImporter
    {
        return new ComponentsImporter(
            $this->getJiraComponentsRetrieverWithComponents($components),
            $this->getJiraComponentLinkedIssuesRetrieverWithIssues($linked_issues),
            new ComponentsTrackerBuilder(),
            $this->getJiraUserRetriever(),
            new NullLogger(),
        );
    }

    private function getJiraUserRetriever(): GetTuleapUserFromJiraUser
    {
        return new class implements GetTuleapUserFromJiraUser {
            public function getAssignedTuleapUser(string $unique_account_identifier): PFUser
            {
                return UserTestBuilder::aUser()->build();
            }

            public function retrieveJiraAuthor(JiraUser $jira_user): PFUser
            {
                return UserTestBuilder::aUser()->withUserName('user01')->build();
            }
        };
    }

    private function getJiraComponentsRetrieverWithoutComponents(): ComponentsRetriever
    {
        return $this->getJiraComponentsRetrieverWithComponents([]);
    }

    /**
     * @param JiraComponent[] $components
     */
    private function getJiraComponentsRetrieverWithComponents(array $components): ComponentsRetriever
    {
        return new class ($components) implements ComponentsRetriever
        {
            /**
             * @var JiraComponent[]
             */
            private $components;

            public function __construct(array $components)
            {
                $this->components = $components;
            }

            public function getProjectComponents(string $jira_project_id): array
            {
                return $this->components;
            }
        };
    }

    private function getJiraComponentLinkedIssuesRetrieverWithoutIssues(): ComponentIssuesRetriever
    {
        return $this->getJiraComponentLinkedIssuesRetrieverWithIssues([]);
    }

    /**
     * @param JiraComponentLinkedIssue[] $linked_issues
     */
    private function getJiraComponentLinkedIssuesRetrieverWithIssues(array $linked_issues): ComponentIssuesRetriever
    {
        return new class ($linked_issues) implements ComponentIssuesRetriever
        {
            /**
             * @var JiraComponentLinkedIssue[]
             */
            private $linked_issues;

            public function __construct(array $linked_issues)
            {
                $this->linked_issues = $linked_issues;
            }

            public function getComponentIssues(JiraComponent $component, string $jira_project_key): array
            {
                return $this->linked_issues;
            }
        };
    }

    private function getXMLAfterExport(ComponentsImporter $jira_components_importer): \SimpleXMLElement
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><project><trackers/></project>');

        $jira_components_importer->importProjectComponents(
            $xml->trackers,
            "project_key",
            new FieldAndValueIDGenerator(),
            UserTestBuilder::aUser()->withUserName('forge__tracker_importer_user')->build(),
        );

        return $xml;
    }
}
