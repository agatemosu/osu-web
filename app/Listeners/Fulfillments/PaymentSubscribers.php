<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Listeners\Fulfillments;

use App\Libraries\Fulfillments\FulfillmentFactory;
use App\Mail\StorePaymentCompleted;
use App\Models\Store\Order;
use App\Traits\StoreNotifiable;
use DB;
use Exception;
use Log;
use Mail;

/**
 * store.payments event dispatcher.
 *
 * Listens to the "store.payments" event stream and dispatches the appropriate
 * messages and commands.
 */
class PaymentSubscribers
{
    use StoreNotifiable;

    public function onPaymentCancelled($eventName, $data)
    {
        $event = $data[0] ?? null;
        $fulfillers = FulfillmentFactory::createFulfillersFor($event->order);
        $count = count($fulfillers);
        $this->notifyOrder($event->order, "dispatching `{$count}` fulfillers", $eventName);

        DB::transaction(function () use ($fulfillers, $event, $eventName) {
            try {
                // This should probably be shoved off into a queue processor somewhere...
                foreach ($fulfillers as $fulfiller) {
                    $fulfiller->revoke();
                }
            } catch (Exception $exception) {
                $this->notifyError($exception, $event->order, $eventName);
                throw $exception;
            }
        });
    }

    public function onPaymentError($eventName, $data)
    {
        // TODO: make notifyError less fruity and more like the other ones.
        $context = array_intersect_key($data, [
            'order_number' => '',
            'notification_type' => '',
            'transaction_id' => '',
        ]);
        $this->notifyError($data['error'], $data['order'], $eventName, $context);
    }

    public function onPaymentPending($eventName, $data)
    {
        $event = $data[0] ?? null;
        $this->notifyOrder($event->order, 'eCheck used; waiting to clear.', $eventName);
    }

    public function subscribe($events)
    {
        $events->listen(
            'store.payments.cancelled.*',
            static::class.'@onPaymentCancelled'
        );
    }
}
