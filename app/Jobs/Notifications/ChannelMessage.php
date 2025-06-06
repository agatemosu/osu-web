<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Jobs\Notifications;

use App\Models\Notification;

class ChannelMessage extends ChannelMessageBase
{
    const NOTIFICATION_OPTION_NAME = Notification::CHANNEL_MESSAGE;
}
