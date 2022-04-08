/**
 * Copyright (c) Enalean, 2022-Present. All Rights Reserved.
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

import type { ArtifactResponse } from "../../../lib/docx";
import { getLinkedArtifacts, getReportArtifacts } from "../rest-querier";
import type { ExportSettings } from "../export-document";
import type { OrganizedReportsData } from "../type";
import { limitConcurrencyPool } from "@tuleap/concurrency-limit-pool";

export async function organizeReportsData(
    export_settings: ExportSettings
): Promise<OrganizedReportsData> {
    const first_level_report_artifacts_responses: ArtifactResponse[] = await getReportArtifacts(
        export_settings.first_level.report_id,
        true
    );

    const artifact_representations_map: Map<number, ArtifactResponse> = new Map();
    const first_level_artifacts_ids_array: Array<number> = [];
    for (const artifact_response of first_level_report_artifacts_responses) {
        artifact_representations_map.set(artifact_response.id, artifact_response);
        first_level_artifacts_ids_array.push(artifact_response.id);
    }

    const second_level_artifacts_ids_array: Array<number> = [];
    if (export_settings.second_level) {
        const second_level_report_artifacts_responses: ArtifactResponse[] =
            await getReportArtifacts(export_settings.second_level.report_id, true);

        const linked_artifacts_representations: ArtifactResponse[] = [];
        await limitConcurrencyPool(
            5,
            first_level_artifacts_ids_array,
            async (artifact_id: number): Promise<void> => {
                for (const artifact_link_type of export_settings.first_level.artifact_link_types) {
                    const linked_artifacts_responses = await getLinkedArtifacts(
                        artifact_id,
                        artifact_link_type
                    );
                    for (const linked_artifacts_response of linked_artifacts_responses) {
                        if (linked_artifacts_response.collection.length === 0) {
                            continue;
                        }
                        linked_artifacts_representations.push(
                            ...linked_artifacts_response.collection
                        );
                    }
                }
            }
        );

        const matching_second_level_representations: ArtifactResponse[] =
            second_level_report_artifacts_responses.filter((value: ArtifactResponse) =>
                linked_artifacts_representations.find(
                    (element: ArtifactResponse) => value.id === element.id
                )
            );

        for (const artifact_response of matching_second_level_representations) {
            artifact_representations_map.set(artifact_response.id, artifact_response);
            second_level_artifacts_ids_array.push(artifact_response.id);
        }
    }

    return {
        artifact_representations: artifact_representations_map,
        first_level_artifacts_ids: first_level_artifacts_ids_array,
        second_level_artifacts_ids: second_level_artifacts_ids_array,
    };
}
