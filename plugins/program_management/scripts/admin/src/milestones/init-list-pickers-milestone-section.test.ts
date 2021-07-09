/**
 * Copyright (c) Enalean, 2021 - Present. All Rights Reserved.
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

import type { GetText } from "@tuleap/core/scripts/tuleap/gettext/gettext-init";
import { initListPickersMilestoneSection } from "./init-list-pickers-milestone-section";
import * as listPicker from "@tuleap/list-picker";

const createDocument = (): Document => document.implementation.createHTMLDocument();

describe("initListPickersMilestoneSection", () => {
    const gettext: GetText = {
        gettext: (msgid: string) => {
            return msgid;
        },
    } as GetText;

    it("When program increment tracker selector does not exist, Then error is thrown", async () => {
        await expect(() =>
            initListPickersMilestoneSection(createDocument(), gettext)
        ).rejects.toThrowError(
            "admin-configuration-program-increment-tracker element does not exist"
        );
    });

    it("When plannable trackers selector does not exist, Then error is thrown", async () => {
        const pi_selector = document.createElement("select");
        pi_selector.id = "admin-configuration-program-increment-tracker";

        const doc = createDocument();
        doc.body.appendChild(pi_selector);

        await expect(() => initListPickersMilestoneSection(doc, gettext)).rejects.toThrowError(
            "admin-configuration-plannable-trackers element does not exist"
        );
    });

    it("When permission prioritize selector does not exist, Then error is thrown", async () => {
        const pi_selector = document.createElement("select");
        pi_selector.id = "admin-configuration-program-increment-tracker";

        const plannable_trackers_selector = document.createElement("select");
        plannable_trackers_selector.id = "admin-configuration-plannable-trackers";

        const doc = createDocument();
        doc.body.appendChild(pi_selector);
        doc.body.appendChild(plannable_trackers_selector);

        await expect(() => initListPickersMilestoneSection(doc, gettext)).rejects.toThrowError(
            "admin-configuration-permission-prioritize element does not exist"
        );
    });

    it("When all sectors exist, Then listpicker is called 3 times", async () => {
        const pi_selector = document.createElement("select");
        pi_selector.id = "admin-configuration-program-increment-tracker";

        const plannable_trackers_selector = document.createElement("select");
        plannable_trackers_selector.id = "admin-configuration-plannable-trackers";

        const permissions_selector = document.createElement("select");
        permissions_selector.id = "admin-configuration-permission-prioritize";

        const doc = createDocument();
        doc.body.setAttribute("data-user-locale", "en-EN");
        doc.body.appendChild(pi_selector);
        doc.body.appendChild(plannable_trackers_selector);
        doc.body.appendChild(permissions_selector);

        const create_list_picker = jest.spyOn(listPicker, "createListPicker");

        await initListPickersMilestoneSection(doc, gettext);
        expect(create_list_picker).toHaveBeenNthCalledWith(1, pi_selector, {
            is_filterable: true,
            locale: "en-EN",
            placeholder: "Choose a source tracker for Program Increments",
        });
        expect(create_list_picker).toHaveBeenNthCalledWith(2, plannable_trackers_selector, {
            is_filterable: true,
            locale: "en-EN",
            placeholder: "Choose which trackers can be planned",
        });
        expect(create_list_picker).toHaveBeenNthCalledWith(3, permissions_selector, {
            is_filterable: true,
            locale: "en-EN",
            placeholder: "Choose who can prioritize and plan items",
        });
    });
});
