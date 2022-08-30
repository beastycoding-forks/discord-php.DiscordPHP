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
use Discord\Parts\Guild\Guild;
use Discord\Parts\Guild\Integration;

/**
 * @link https://discord.com/developers/docs/topics/gateway#integration-update
 *
 * @since 7.0.0
 */
class IntegrationUpdate extends Event
{
    /**
     * @inheritdoc
     */
    public function handle($data)
    {
            $integrationPart = $oldIntegration = null;

            /** @var ?Guild */
            if ($guild = yield $this->discord->guilds->cacheGet($data->guild_id)) {
                /** @var ?Integration */
                if ($oldIntegration = $guild->integrations[$data->id]) {
                    // Swap
                    $integrationPart = $oldIntegration;
                    $oldIntegration = clone $oldIntegration;

                    $integrationPart->fill((array) $data);
                }
            }

            if ($integrationPart === null) {
                /** @var Integration */
                $integrationPart = $this->factory->create(Integration::class, $data, true);
            }

            if ($guild) {
                yield $guild->integrations->cache->set($data->id, $integrationPart);
            }

            if (isset($data->user)) {
                $this->cacheUser($data->user);
            }

            return [$integrationPart, $oldIntegration];
    }
}