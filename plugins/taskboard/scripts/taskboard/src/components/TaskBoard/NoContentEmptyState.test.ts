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

import { Vue } from "vue/types/vue";
import { shallowMount } from "@vue/test-utils";
import { createTaskboardLocalVue } from "../../helpers/local-vue-for-test";
import NoContentEmptyState from "./NoContentEmptyState.vue";
import { createStoreMock } from "@tuleap-vue-components/store-wrapper-jest";

describe("NoContentEmptyState", () => {
    let local_vue: typeof Vue;

    beforeEach(() => {
        local_vue = createTaskboardLocalVue();
    });

    it("displays a cell that span on the whole table", () => {
        const wrapper = shallowMount(NoContentEmptyState, {
            localVue: local_vue,
            mocks: {
                $store: createStoreMock({
                    state: { columns: [{ id: 2, label: "To do" }, { id: 3, label: "Done" }] }
                })
            }
        });
        expect(wrapper.element).toMatchSnapshot();
    });
});
