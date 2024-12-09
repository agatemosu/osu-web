// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type { BeatmapReviewDiscussionType } from 'interfaces/beatmap-discussion-review';
import type { BaseEditor } from 'slate';
import type { HistoryEditor } from 'slate-history';
import type { ReactEditor } from 'slate-react';

interface CustomText {
  bold?: boolean;
  italic?: boolean;
  text: string;
  timestamp?: string; // added by decorateTimestamp
}

interface EmbedElement {
  beatmapId?: number | null;
  children: CustomText[];
  discussionId?: number;
  discussionType: BeatmapReviewDiscussionType;
  // TODO: timestamp should imply/require beatmapId
  timestamp?: string;
  type: 'embed';
}

interface ParagraphElement {
  children: CustomText[];
  type: 'paragraph';
}

type CustomEditor = BaseEditor & ReactEditor & HistoryEditor;
type CustomElement = EmbedElement | ParagraphElement;
declare module 'slate' {
  interface CustomTypes {
    Editor: CustomEditor;
    Element: CustomElement;
    Text: CustomText;
  }
}
