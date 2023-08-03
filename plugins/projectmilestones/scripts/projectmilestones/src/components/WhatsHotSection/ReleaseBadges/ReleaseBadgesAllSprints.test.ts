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

import type { ShallowMountOptions, Wrapper } from "@vue/test-utils";
import { shallowMount } from "@vue/test-utils";
import ReleaseBadgesAllSprints from "./ReleaseBadgesAllSprints.vue";
import type { MilestoneData, TrackerProjectLabel } from "../../../type";
import { createReleaseWidgetLocalVue } from "../../../helpers/local-vue-for-test";
import { createTestingPinia } from "@pinia/testing";
import { defineStore } from "pinia";

let release_data: MilestoneData & Required<Pick<MilestoneData, "planning">>;
const total_sprint = 10;
const initial_effort = 10;
const component_options: ShallowMountOptions<ReleaseBadgesAllSprints> = {};

const project_id = 102;

describe("ReleaseBadgesAllSprints", () => {
    async function getPersonalWidgetInstance(
        user_can_view_sub_milestones_planning: boolean,
    ): Promise<Wrapper<ReleaseBadgesAllSprints>> {
        const useStore = defineStore("root", {
            state: () => ({
                project_id: project_id,
                user_can_view_sub_milestones_planning,
            }),
        });
        const pinia = createTestingPinia();
        useStore(pinia);

        component_options.localVue = await createReleaseWidgetLocalVue();

        return shallowMount(ReleaseBadgesAllSprints, component_options);
    }

    beforeEach(() => {
        release_data = {
            id: 2,
            total_sprint,
            initial_effort,
            resources: {
                milestones: {
                    accept: {
                        trackers: [
                            {
                                label: "Sprint1",
                            },
                        ],
                    },
                },
            },
        } as MilestoneData;

        component_options.propsData = { release_data };
    });

    describe("Display number of sprint", () => {
        it("When there is a tracker, Then number of sprint is displayed", async () => {
            const wrapper = await getPersonalWidgetInstance(true);

            expect(wrapper.get("[data-test=badge-sprint]").text()).toBe("10 Sprint1");
        });

        it("When there isn't tracker, Then there is no link", async () => {
            release_data = {
                id: 2,
                total_sprint,
                initial_effort,
                resources: {
                    milestones: {
                        accept: {
                            trackers: [] as TrackerProjectLabel[],
                        },
                    },
                },
            } as MilestoneData;

            component_options.propsData = {
                release_data,
            };
            const wrapper = await getPersonalWidgetInstance(true);

            expect(wrapper.find("[data-test=badge-sprint]").exists()).toBe(false);
        });

        it("When the user can't see the tracker, Then number of sprint is not displayed", async () => {
            const wrapper = await getPersonalWidgetInstance(false);

            expect(wrapper.find("[data-test=badge-sprint]").exists()).toBe(false);
        });
    });
});
