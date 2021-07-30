/**
 * Copyright (c) Enalean, 2021-Present. All Rights Reserved.
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

import type { ExportDocument } from "../type";
import {
    AlignmentType,
    File,
    Footer,
    Packer,
    PageNumber,
    Paragraph,
    TextRun,
    HeadingLevel,
    StyleLevel,
    Bookmark,
    PageBreak,
} from "docx";
import { TableOfContentsPrefilled } from "./DOCX/TableOfContents/table-of-contents";
import { getAnchorToArtifactContent } from "./DOCX/sections-anchor";

const HEADER_STYLE_ARTIFACT_TITLE = "ArtifactTitle";
const HEADER_LEVEL_ARTIFACT_TITLE = HeadingLevel.HEADING_6;

export async function downloadDocx(document: ExportDocument): Promise<void> {
    const footers = {
        default: new Footer({
            children: [
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    children: [
                        new TextRun({
                            children: [PageNumber.CURRENT, " / ", PageNumber.TOTAL_PAGES],
                        }),
                    ],
                }),
            ],
        }),
    };

    const artifacts_content = [];
    for (const artifact of document.artifacts) {
        artifacts_content.push(
            new Paragraph({
                heading: HEADER_LEVEL_ARTIFACT_TITLE,
                style: HEADER_STYLE_ARTIFACT_TITLE,
                children: [
                    new Bookmark({
                        id: getAnchorToArtifactContent(artifact),
                        children: [new TextRun(artifact.title)],
                    }),
                ],
            })
        );
        for (const artifact_value of artifact.fields) {
            artifacts_content.push(
                new Paragraph({
                    text: artifact_value.field_name + "\n" + artifact_value.field_value,
                })
            );
        }
    }

    const table_of_contents = new TableOfContentsPrefilled(document.artifacts, {
        hyperlink: true,
        stylesWithLevels: [
            new StyleLevel("ArtifactTitle", Number(HEADER_LEVEL_ARTIFACT_TITLE.substr(-1))),
        ],
    });

    const file = new File({
        styles: {
            paragraphStyles: [
                {
                    id: HEADER_STYLE_ARTIFACT_TITLE,
                    name: HEADER_STYLE_ARTIFACT_TITLE,
                    basedOn: HEADER_LEVEL_ARTIFACT_TITLE,
                    next: HEADER_LEVEL_ARTIFACT_TITLE,
                    quickFormat: true,
                },
            ],
        },
        sections: [
            {
                children: [
                    table_of_contents,
                    new Paragraph({ children: [new PageBreak()] }),
                    ...artifacts_content,
                ],
                footers,
            },
        ],
    });
    await triggerDownload(document.name, file);
}

async function triggerDownload(filename: string, file: File): Promise<void> {
    const blob = await Packer.toBlob(file);
    const download_link = document.createElement("a");
    const object_url = URL.createObjectURL(blob);
    download_link.href = object_url;
    download_link.setAttribute("download", `${filename}.docx`);
    document.body.appendChild(download_link);
    download_link.click();
    download_link.remove();
    URL.revokeObjectURL(object_url);
}
