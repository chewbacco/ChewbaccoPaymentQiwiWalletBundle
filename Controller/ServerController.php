<?php

namespace Chewbacco\Payment\QiwiWalletBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Payment\CoreBundle\PluginController\Result;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;
use JMS\Payment\CoreBundle\Model\PaymentInterface;

class ServerController extends Controller
{
    /** @DI\Inject("payment.plugin_controller") */
    private $ppc;

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $callback = function ($bill) use (&$em) {
            $payment_repo = $em->getRepository('JMSPaymentCoreBundle:Payment');

            $payment = $payment_repo->findOneById($bill->id);
            if (!$payment) {
                throw new \Exception('Неправильный код чека');
            }
            if (!$payment->getPaymentInstruction()->getState() == PaymentInstructionInterface::STATE_VALID) {
                return;
            }

            //Оплачен
            if ($bill->status->getCode() == 60) {
                $result = $this->ppc->approveAndDeposit($payment->getId(), $payment->getApprovingAmount());

                if (Result::STATUS_SUCCESS !== $result->getStatus()) {
                    throw new \RuntimeException('Transaction was not successful: '.$result->getReasonCode());
                }
                $this->ppc->closePaymentInstruction($payment->getPaymentInstruction());
            } elseif ($bill->status->getCode() == 161) { //'Отменен (Истекло время)'
                $payment->setState(PaymentInterface::STATE_EXPIRED);
                $payment->setExpired(true);
                $payment->getPaymentInstruction()->setStatus(PaymentInstructionInterface::STATE_INVALID);
                $em->persist($payment);
                $em->persist($payment->getPaymentInstruction());
                $em->flush();

            } elseif ($bill->status->getCode() >= 100) { /*100 и выше - отменены (ошибка)*/
                $payment->setState(PaymentInterface::STATE_CANCELED);
                $payment->getPaymentInstruction()->setStatus(PaymentInstructionInterface::STATE_INVALID);
                $em->persist($payment);
                $em->persist($payment->getPaymentInstruction());
            }

            return; // Код возврата для сервера QIWI. 0 - все нормально
        };

        ob_start();
        $this->get('chewbacco_payment_qiwi_wallet.client')->processRequest($callback);
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent(ob_get_clean());

        return $response;
    }
}
