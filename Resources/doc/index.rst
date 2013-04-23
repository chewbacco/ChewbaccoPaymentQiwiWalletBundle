============
Installation
============
Dependencies
------------
This plugin depends on the JMSPaymentCoreBundle_, so you'll need to add this to your kernel
as well even if you don't want to use its persistence capabilities.

Configuration
-------------

::

    // config.yml
    chewbacco_payment_qiwi_wallet:
        login: your username 
        password: your password 


::

    // routing.yml
    chewbacco_payment_qiwi_wallet:
        resource: "@ChewbaccoPaymentQiwiWalletBundle/Resources/config/routing.yml" 
        prefix:   /chewbacco/payment/qiwi_wallet

=====
Usage
=====
http://jmsyst.com/bundles/JMSPaymentCoreBundle/master/usage

.. _JMSPaymentCoreBundle: https://github.com/schmittjoh/JMSPaymentCoreBundle/blob/master/Resources/doc/index.rst
