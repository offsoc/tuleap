/**
 * Copyright (c) Enalean, 2022 - present. All Rights Reserved.
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
import type { AdvancedSearchParams } from "../type";
import type { Dictionary } from "vue-router/types/router";

export function getRouterQueryFromSearchParams(params: AdvancedSearchParams): Dictionary<string> {
    const query: Dictionary<string> = {};
    if (params.query.length > 0) {
        query.q = params.query;
    }
    if (params.type.length > 0) {
        query.type = params.type;
    }
    if (params.title.length > 0) {
        query.title = params.title;
    }
    if (params.description.length > 0) {
        query.description = params.description;
    }

    return query;
}
