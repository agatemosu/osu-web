// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

import type MessageJson from 'interfaces/chat/message-json';
import type { UserJson } from 'interfaces/user-json';

export default interface MessagesNewJson {
  messages: MessageJson[];
  users: UserJson[];
}
