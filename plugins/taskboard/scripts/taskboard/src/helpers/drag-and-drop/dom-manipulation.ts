/*
 * Copyright (c) Enalean, 2020-Present. All Rights Reserved.
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

import { DrekkenovInitOptions } from "./types";

export function cloneHTMLElement(element: HTMLElement): HTMLElement {
    // eslint-disable-next-line @typescript-eslint/consistent-type-assertions
    return element.cloneNode(true) as HTMLElement;
}

export function findClosestDraggable(
    options: DrekkenovInitOptions,
    element: Node
): HTMLElement | null {
    let current_element: Node | null = element;
    const handle = current_element;
    if (!(handle instanceof HTMLElement)) {
        return null;
    }
    do {
        if (
            current_element instanceof HTMLElement &&
            options.isInvalidDragHandle(current_element, handle)
        ) {
            return null;
        }
        if (current_element instanceof HTMLElement && options.isDraggable(current_element)) {
            return current_element;
        }
        current_element = current_element.parentNode;
    } while (current_element !== null);

    return null;
}

export function findClosestDropzone(
    options: DrekkenovInitOptions,
    element: Node
): HTMLElement | null {
    let current_element: Node | null = element;
    do {
        if (current_element instanceof HTMLElement && options.isDropZone(current_element)) {
            return current_element;
        }
        current_element = current_element.parentNode;
    } while (current_element !== null);

    return null;
}

export function findClosestElementBeforeYCoordinate(
    y_coordinate: number,
    children: Element[]
): Element | null {
    let candidate = null;
    for (const child of children) {
        const rect = child.getBoundingClientRect();
        if (rect.top > y_coordinate) {
            return candidate;
        }
        candidate = child;
    }
    return null;
}

export function insertAfter(
    dropzone_element: Element,
    drop_ghost: Element,
    reference_element: Element
): void {
    dropzone_element.insertBefore(drop_ghost, reference_element.nextElementSibling);
}
