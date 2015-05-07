<?php

namespace Chewbacco\Payment\QiwiWalletBundle\Plugin;

use JMS\Payment\CoreBundle\Plugin\PluginInterface;
use JMS\Payment\CoreBundle\Plugin\AbstractPlugin;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;
use JMS\Payment\CoreBundle\Plugin\ErrorBuilder;
use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\FinancialException;

use Werkint\Qiwi\Client;
/*
 * Copyright 2012 Dmitry R. Tsoy <hd.deman@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class QiwiWalletPlugin extends AbstractPlugin
{
    /**
     * @var string
     */
    protected $returnUrl;

    /**
     * @var \JMS\Payment\PaypalBundle\Client\Client
     */
    protected $client;

    /**
     * @param string              $returnUrl url to go to after a successful billing
     * @param Werkint\Qiwi\Client $client
     */
    public function __construct($returnUrl, Client $client)
    {
        $this->client = $client;
        $this->returnUrl = $returnUrl;
    }

    public function checkPaymentInstruction(PaymentInstructionInterface $instruction)
    {
        $errorBuilder = new ErrorBuilder();
        $data = $instruction->getExtendedData();

        if (!$data->get('number')) {
            $errorBuilder->addDataError('number', 'form.error.required');
        }

        if ($instruction->getAmount() > 15000) {
            $errorBuilder->addGlobalError('form.error.qiwi_wallet_max_limit_exceeded');
        }

        if ($errorBuilder->hasErrors()) {
            throw $errorBuilder->getException();
        }
    }

    public function approveAndDeposit(FinancialTransactionInterface $transaction, $retry)
    {
        $data = $transaction->getExtendedData();

        //@TOTO throw ex if $transaction->getPayment()->getPaymentInstruction()->getCurrency() != RUR

        if (!$retry) {
            $bill = $this->client->createBill(
                preg_replace('~[\D]~', '', $data->get('number')),
                $transaction->getRequestedAmount(),
                $transaction->getPayment()->getId(),
                $data->get('comment'),
                $data->get('lifetime'),
                $data->get('alarm'),
                $data->get('create')
            );

            if ($bill->getCode()) {
                $ex = new FinancialException('Payment is not successful: '.$bill->getCode());
                $ex->setFinancialTransaction($transaction);
                $transaction->setResponseCode('Failed');
                $transaction->setReasonCode($bill->getCode());
                throw $ex;
            }

            $actionRequest = new ActionRequiredException('Successful');
            $actionRequest->setFinancialTransaction($transaction);
            $actionRequest->setAction(new VisitUrl($data->get('return_url')));

            $transaction->setResponseCode(PluginInterface::RESPONSE_CODE_PENDING);
            $transaction->setReasonCode(PluginInterface::REASON_CODE_ACTION_REQUIRED);
            throw $actionRequest;

        } else {

            $authorizationId = $transaction->getPayment()->getApproveTransaction()->getReferenceNumber();

            $transaction->setReferenceNumber($authorizationId);
            $transaction->setProcessedAmount($transaction->getPayment()->getPaymentInstruction()->getApprovingAmount());
            $transaction->setResponseCode(PluginInterface::RESPONSE_CODE_SUCCESS);
            $transaction->setReasonCode(PluginInterface::REASON_CODE_SUCCESS);
        }

    }

    public function processes($method)
    {
        return 'qiwi_wallet' === $method;
    }
}
