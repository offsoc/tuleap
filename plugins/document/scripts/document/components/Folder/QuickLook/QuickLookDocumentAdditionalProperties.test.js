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
 * along with Tuleap. If not, see http://www.gnu.org/licenses/.
 *
 */

import { shallowMount } from "@vue/test-utils";
import QuickLookDocumentAdditionalProperties from "./QuickLookDocumentAdditionalProperties.vue";

import localVue from "../../../helpers/local-vue";
import { createStoreMock } from "@tuleap/vuex-store-wrapper-jest";

describe("QuickLookDocumentAdditionalProperties", () => {
    let properties_factory, state, store;

    beforeEach(() => {
        state = { configuration: {} };

        const store_options = { state };

        store = createStoreMock(store_options);

        properties_factory = (props = {}) => {
            return shallowMount(QuickLookDocumentAdditionalProperties, {
                localVue,
                propsData: { property: props },
                mocks: { $store: store },
            });
        };
    });
    describe(`property name`, () => {
        it(`Given an Obsolescence Date property
             Then it displays "Validity" for the label`, () => {
            const property_date = {
                id: 100,
                short_name: "obsolescence_date",
                name: "Obsolescence Date",
                type: "date",
                list_value: null,
                value: "2019-08-02",
                post_processed_value: "2019-08-02",
            };

            const wrapper = properties_factory(property_date);

            const label_element = wrapper.get("[data-test=properties-list-label]");
            expect(label_element).toBeTruthy();
            expect(label_element.text()).toBe("Validity");
        });
    });

    describe(`List type properties`, () => {
        it(`Given a list value with several value
             Then it displays the list value in a ul tag`, () => {
            const list_property = {
                id: 100,
                name: "original name",
                type: "list",
                list_value: [
                    { id: 1, name: "value 1" },
                    { id: 2, name: "fail" },
                    { id: 3, name: "Tea" },
                ],
                value: null,
                post_processed_value: null,
            };
            const wrapper = properties_factory(list_property);

            const value_list_element = wrapper.findAll("li");

            expect(value_list_element).toHaveLength(3);
            expect(value_list_element.at(0).text()).toBe("value 1");
            expect(value_list_element.at(1).text()).toBe("fail");
            expect(value_list_element.at(2).text()).toBe("Tea");
        });
        it(`Given a list value with one value
             Then it displays the value`, () => {
            const list_property = {
                id: 100,
                name: "original name",
                type: "list",
                list_value: [{ id: 1, name: "value 1" }],
                value: null,
                post_processed_value: null,
            };
            const wrapper = properties_factory(list_property);

            expect(wrapper.find("ul").exists()).toBeFalsy();
            expect(wrapper.get("p").text()).toBe("value 1");
        });
    });

    describe("Properties simple string value", () => {
        it(`Given text type value
    Then it displays the value`, () => {
            const string_property = {
                id: 100,
                name: "Bad lyrics",
                short_name: "bad-lyrics",
                type: "text",
                list_value: null,
                value: "The mer-custo wants ref #1 that ... mmmmmh, mmmmh ...",
                post_processed_value:
                    'The mer-custo wants <a href="https://example.com/goto">ref #1</a> that ... mmmmmh, mmmmh ...',
            };

            const wrapper = properties_factory(string_property);

            const display_properties = wrapper.get("[id=document-bad-lyrics]");

            expect(wrapper.find("ul").exists()).toBeFalsy();
            expect(wrapper.find("[data-test=property-list-date]").exists()).toBeFalsy();
            expect(display_properties).toBeTruthy();
            expect(display_properties.text()).toEqual(string_property.value);
            expect(display_properties.html()).toContain(string_property.post_processed_value);
        });
    });
    it(`Given text type empty value
    Then it displays the value`, () => {
        const empty_property = {
            id: 100,
            name: "silence",
            short_name: "silence",
            type: "text",
            list_value: null,
            value: "",
            post_processed_value: "",
        };

        const wrapper = properties_factory(empty_property);

        const display_properties = wrapper.get("[id=document-silence]");

        expect(wrapper.find("ul").exists()).toBeFalsy();
        expect(wrapper.find("[data-test=property-list-date]").exists()).toBeFalsy();
        expect(display_properties.text()).toBeTruthy();
        expect(display_properties).not.toBe("Permanent");
        expect(display_properties.text()).toBe("Empty");
        expect(display_properties.text()).not.toEqual(empty_property.value);
    });
});
