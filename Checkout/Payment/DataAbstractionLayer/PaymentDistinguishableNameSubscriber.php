<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Payment\DataAbstractionLayer;

use Shopware\Core\Checkout\Payment\PaymentEvents;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\Feature;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal (flag:FEATURE_NEXT_15170)
 */
class PaymentDistinguishableNameSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PaymentEvents::PAYMENT_METHOD_LOADED_EVENT => 'addDistinguishablePaymentName',
        ];
    }

    public function addDistinguishablePaymentName(EntityLoadedEvent $event): void
    {
        if (!Feature::isActive('FEATURE_NEXT_15170')) {
            return;
        }

        /** @var PaymentMethodEntity $payment */
        foreach ($event->getEntities() as $payment) {
            if ($payment->getTranslation('distinguishableName') === null) {
                $payment->addTranslated('distinguishableName', $payment->getTranslation('name'));
            }
            if ($payment->getDistinguishableName() === null) {
                $payment->setDistinguishableName($payment->getName());
            }
        }
    }
}