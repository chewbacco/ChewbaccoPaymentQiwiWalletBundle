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

    // YAML
    jms_payment_paypal:
        username: your api username (not your account username)
        password: your api password (not your account password)
        signature: your api signature
        debug: true/false # when true, connect to PayPal sandbox; uses kernel debug value when not specified


=====
Usage
=====
http://jmsyst.com/bundles/JMSPaymentCoreBundle/master/usage

.. _JMSPaymentCoreBundle: https://github.com/schmittjoh/JMSPaymentCoreBundle/blob/master/Resources/doc/index.rst
