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

import { AfterDropEventSource, AfterDropListener, DragDropHandlers } from "./types";

export class DocumentEventsHandler implements AfterDropListener {
    constructor(
        event_source: AfterDropEventSource,
        private readonly handlers: DragDropHandlers,
        private readonly document: Document
    ) {
        event_source.attachAfterDropListener(this);
    }

    public attachDragDropListeners(): void {
        this.document.addEventListener("dragenter", this.handlers.dragEnterHandler);
        this.document.addEventListener("dragleave", this.handlers.dragLeaveHandler);
        this.document.addEventListener("dragover", this.handlers.dragOverHandler);
        this.document.addEventListener("dragend", this.handlers.dragEndHandler);
        this.document.addEventListener("drop", this.handlers.dropHandler);
    }

    public afterDrop(): void {
        this.document.removeEventListener("dragenter", this.handlers.dragEnterHandler);
        this.document.removeEventListener("dragleave", this.handlers.dragLeaveHandler);
        this.document.removeEventListener("dragover", this.handlers.dragOverHandler);
        this.document.removeEventListener("dragend", this.handlers.dragEndHandler);
        this.document.removeEventListener("drop", this.handlers.dropHandler);
    }
}
