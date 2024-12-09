// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type ChannelJson from 'interfaces/chat/channel-json';
import type ChatSilenceJson from 'interfaces/chat/chat-silence-json';
import type MessageJson from 'interfaces/chat/message-json';

export default interface ChatUpdatesJson {
  messages?: MessageJson[];
  presence?: ChannelJson[];
  silences?: ChatSilenceJson[];
}
