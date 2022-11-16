/*
 * Copyright (c) Enalean, 2018-Present. All Rights Reserved.
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

import { TAG_NAME as INLINE_COMMENT_NAME } from "../../comments/PullRequestComment.ts";
import { NAME as NEW_INLINE_COMMENT_NAME } from "../new-inline-comment-component";
import { getDisplayAboveLineForWidget } from "./side-by-side-placeholder-positioner.js";
import { getCommentPlaceholderWidget } from "./side-by-side-comment-placeholder-widget-finder.ts";
import { doesHandleHaveWidgets } from "./side-by-side-line-widgets-helper.ts";

export function equalizeSides(left_code_mirror, right_code_mirror, handles) {
    if (typeof handles === "undefined") {
        // Do nothing
        return null;
    }

    const { left_handle, right_handle } = handles;
    const left_line_height = getTotalHeight(left_handle);
    const right_line_height = getTotalHeight(right_handle);

    if (left_line_height === right_line_height) {
        // nothing to do, all is already perfect
        return null;
    }

    if (left_line_height > right_line_height) {
        return adjustHeights(
            left_handle,
            left_line_height,
            left_code_mirror,
            right_handle,
            right_line_height,
            right_code_mirror
        );
    }

    return adjustHeights(
        right_handle,
        right_line_height,
        right_code_mirror,
        left_handle,
        left_line_height,
        left_code_mirror
    );
}

function getSumOfWidgetsHeights(widgets) {
    return widgets
        .map((widget) => {
            if (isCommentWidget(widget)) {
                return widget.node.getBoundingClientRect().height;
            }

            return widget.height;
        })
        .reduce((sum, value) => sum + value, 0);
}

function getTotalHeight(handle) {
    if (!doesHandleHaveWidgets(handle)) {
        return 0;
    }

    const widgets = handle.widgets.filter(
        (widget) => isCommentWidget(widget) || isCommentPlaceholderWidget(widget)
    );

    return getSumOfWidgetsHeights(widgets);
}

function getCommentsHeight(handle) {
    if (!doesHandleHaveWidgets(handle)) {
        return 0;
    }

    const comments_widgets = handle.widgets.filter((widget) => isCommentWidget(widget));

    if (!comments_widgets.length) {
        return 0;
    }

    return getSumOfWidgetsHeights(comments_widgets);
}

function isCommentWidget(line_widget) {
    return (
        line_widget.node.localName === NEW_INLINE_COMMENT_NAME ||
        line_widget.node.localName === INLINE_COMMENT_NAME
    );
}

function isCommentPlaceholderWidget(line_widget) {
    return line_widget.node.className.includes("pull-request-file-diff-comment-placeholder-block");
}

function adjustPlaceholderHeight(placeholder, widget_height) {
    const height = Math.max(widget_height, 0);
    placeholder.node.style.height = `${height}px`;

    placeholder.changed();
}

function adjustHeights(
    handle,
    line_height,
    code_mirror,
    opposite_handle,
    opposite_line_height,
    opposite_code_mirror
) {
    const placeholder = getCommentPlaceholderWidget(handle);
    let optimum_height = opposite_line_height - getCommentsHeight(handle);

    if (!placeholder || optimum_height < 0) {
        const opposite_placeholder = getCommentPlaceholderWidget(opposite_handle);

        if (!opposite_placeholder) {
            optimum_height = line_height - getCommentsHeight(opposite_handle);

            const display_above_line = getDisplayAboveLineForWidget(handle);

            return {
                code_mirror: opposite_code_mirror,
                handle: opposite_handle,
                widget_height: optimum_height,
                display_above_line,
                is_comment_placeholder: true,
            };
        }

        optimum_height = line_height - getCommentsHeight(opposite_handle);

        return adjustPlaceholderHeight(opposite_placeholder, optimum_height);
    }

    return adjustPlaceholderHeight(placeholder, optimum_height);
}
