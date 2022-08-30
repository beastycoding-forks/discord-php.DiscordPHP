<?php

/*
 * This file is a part of the DiscordPHP project.
 *
 * Copyright (c) 2015-present David Cole <david.cole1340@gmail.com>
 *
 * This file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Discord\WebSockets\Events;

use Discord\WebSockets\Event;
use Discord\Parts\User\User;

/**
 * @link https://discord.com/developers/docs/topics/gateway#user-update
 *
 * @since 7.0.0
 */
class UserUpdate extends Event
{
    /**
     * @inheritdoc
     */
    public function handle($data)
    {
        $oldUser = null;

        /** @var User */
        if ($oldUser = $this->discord->users[$data->id]) {
            $userPart = clone $oldUser;
            $userPart->fill((array) $data);
        } else {
            /** @var User */
            $userPart = $this->factory->create(User::class, $data, true);
        }

        return [$userPart, $oldUser];
    }
}