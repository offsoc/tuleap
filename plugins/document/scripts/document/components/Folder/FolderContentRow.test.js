/*
 * Copyright (c) Enalean, 2019 - present. All Rights Reserved.
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

import { createStoreMock } from "@tuleap/vuex-store-wrapper-jest";
import localVue from "../../helpers/local-vue";
import { shallowMount } from "@vue/test-utils";
import FolderContentRow from "./FolderContentRow.vue";
import { TYPE_FILE } from "../../constants";
import emitter from "../../helpers/emitter";

jest.mock("../../helpers/emitter");

function getFolderContentRowInstance(store, props, data = {}) {
    return shallowMount(FolderContentRow, {
        localVue,
        propsData: props,
        mocks: { $store: store },
        data() {
            return { ...data };
        },
        stubs: {
            "tlp-relative-date": true,
        },
    });
}

describe("FolderContentRow", () => {
    let item, store_options, store, wrapper;
    beforeEach(() => {
        item = {
            id: 42,
            title: "my item",
            is_uploading: false,
            is_uploading_new_version: false,
            is_uploading_in_collapsed_folder: false,
            type: TYPE_FILE,
            file_type: "text",
        };

        store_options = {
            state: {
                folded_items_ids: [],
                configuration: { date_time_format: "Y-m-d", project_id: 101 },
                current_folder: {},
                folder_content: [],
            },
        };

        store = createStoreMock(store_options);

        emitter.emit.mockClear();
    });

    describe("Quick look and dropdown menu rendering", () => {
        it("Should render the quick look button and the dropdown menu when no upload action is in progress", () => {
            wrapper = getFolderContentRowInstance(store, {
                item,
                isQuickLookDisplayed: false,
            });

            expect(wrapper.find("[data-test=quick-look-button]").exists()).toBeTruthy();
            expect(wrapper.find("[data-test=dropdown-button]").exists()).toBeTruthy();
            expect(wrapper.find("[data-test=dropdown-menu]").exists()).toBeTruthy();
        });

        it("Should not render the quick look button and the dropdown menu when the item is being uploaded in a collapsed folder", () => {
            item.is_uploading_in_collapsed_folder = true;

            wrapper = getFolderContentRowInstance(store, {
                item,
                isQuickLookDisplayed: false,
            });

            expect(wrapper.find("[data-test=quick-look-button]").exists()).toBeFalsy();
            expect(wrapper.find("[data-test=dropdown-button]").exists()).toBeFalsy();
            expect(wrapper.find("[data-test=dropdown-menu]").exists()).toBeFalsy();
        });

        it("Should not render the quick look button and the dropdown menu when the item is being uploaded", () => {
            item.is_uploading = true;

            wrapper = getFolderContentRowInstance(store, {
                item,
                isQuickLookDisplayed: false,
            });

            expect(wrapper.find("[data-test=quick-look-button]").exists()).toBeFalsy();
            expect(wrapper.find("[data-test=dropdown-button]").exists()).toBeFalsy();
            expect(wrapper.find("[data-test=dropdown-menu]").exists()).toBeFalsy();
        });

        it("Should not render the quick look button and the dropdown menu when a new version of the item is being uploaded", () => {
            item.is_uploading_new_version = true;

            wrapper = getFolderContentRowInstance(store, {
                item,
                isQuickLookDisplayed: false,
            });

            expect(wrapper.find("[data-test=quick-look-button]").exists()).toBeFalsy();
            expect(wrapper.find("[data-test=dropdown-button]").exists()).toBeFalsy();
            expect(wrapper.find("[data-test=dropdown-menu]").exists()).toBeFalsy();
        });
    });

    describe("Progress bar rendering", () => {
        describe("When the quick look pane is open", () => {
            it("Should render the progress bar when the quick look pane is open and the item is being uploaded in a collapsed folder", () => {
                item.is_uploading_in_collapsed_folder = true;

                wrapper = getFolderContentRowInstance(store, {
                    item,
                    isQuickLookDisplayed: true,
                });

                expect(
                    wrapper.find("[data-test=progress-bar-quick-look-pane-open]").exists()
                ).toBeTruthy();

                expect(wrapper.find(".document-tree-cell-owner").exists()).toBeFalsy();
                expect(wrapper.find(".document-tree-cell-updatedate").exists()).toBeFalsy();
            });

            it("Should render the progress bar when the quick look pane is open and a new version of the item is being uploaded", () => {
                item.is_uploading_new_version = true;

                wrapper = getFolderContentRowInstance(store, {
                    item,
                    isQuickLookDisplayed: true,
                });

                expect(
                    wrapper.find("[data-test=progress-bar-quick-look-pane-open]").exists()
                ).toBeTruthy();

                expect(wrapper.find(".document-tree-cell-owner").exists()).toBeFalsy();
                expect(wrapper.find(".document-tree-cell-updatedate").exists()).toBeFalsy();
            });
        });

        describe("When the quick-look pane is closed", () => {
            it("Should render the progress bar when the quick look pane is closed and the item is being uploaded in a collapsed folder", () => {
                item.is_uploading_in_collapsed_folder = true;

                wrapper = getFolderContentRowInstance(store, {
                    item,
                    isQuickLookDisplayed: false,
                });

                expect(
                    wrapper.find("[data-test=progress-bar-quick-look-pane-closed]").exists()
                ).toBeTruthy();

                expect(wrapper.find(".document-tree-cell-owner").exists()).toBeFalsy();
                expect(wrapper.find(".document-tree-cell-updatedate").exists()).toBeFalsy();
            });
        });

        it("Should render the progress bar when the quick look pane is closed and a new version of the item is being uploaded", () => {
            item.is_uploading_new_version = true;

            wrapper = getFolderContentRowInstance(store, {
                item,
                isQuickLookDisplayed: false,
            });

            expect(
                wrapper.find("[data-test=progress-bar-quick-look-pane-closed]").exists()
            ).toBeTruthy();

            expect(wrapper.find(".document-tree-cell-owner").exists()).toBeFalsy();
            expect(wrapper.find(".document-tree-cell-updatedate").exists()).toBeFalsy();
        });
    });

    describe("User badge and last update date rendering", () => {
        it("Should render the user badge and the last update date only when the quick look pane is closed", () => {
            wrapper = getFolderContentRowInstance(store, {
                item,
                isQuickLookDisplayed: false,
            });

            expect(wrapper.find(".document-tree-cell-owner").exists()).toBeTruthy();
            expect(wrapper.find(".document-tree-cell-updatedate").exists()).toBeTruthy();
        });

        it("Should not render the user badge and the last update date when the quick look pane is open", () => {
            wrapper = getFolderContentRowInstance(store, {
                item,
                isQuickLookDisplayed: true,
            });

            expect(wrapper.find(".document-tree-cell-owner").exists()).toBeFalsy();
            expect(wrapper.find(".document-tree-cell-updatedate").exists()).toBeFalsy();
        });
    });

    describe("test toggle-quick-look event emission", () => {
        it("Should emit toggle-quick-look event if no dropdown is displayed", () => {
            const emitter_emit = jest.spyOn(emitter, "emit");

            wrapper = getFolderContentRowInstance(
                store,
                {
                    item,
                },
                { is_dropdown_displayed: false }
            );

            wrapper.find("[data-test=document-folder-content-row]").trigger("click");

            expect(emitter_emit).toHaveBeenCalledWith("toggle-quick-look", {
                details: { item },
            });
        });

        it("Should not emit toggle-quick-look event if a dropdown is displayed", () => {
            const emitter_emit = jest.spyOn(emitter, "emit");

            wrapper = getFolderContentRowInstance(
                store,
                {
                    item,
                },
                { is_dropdown_displayed: true }
            );

            wrapper.find("[data-test=document-folder-content-row]").trigger("click");

            expect(emitter_emit).not.toHaveBeenCalledWith("toggle-quick-look", {
                details: { item },
            });
        });
    });
});
