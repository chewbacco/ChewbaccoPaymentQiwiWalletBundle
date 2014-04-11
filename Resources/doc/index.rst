============
Installation
============
1. Using Composer (recommended)
-------------------------------

To install ChewbaccoPaymentQiwiWalletBundle with Composer just add the following to your
`composer.json` file:

.. code-block :: js

    // composer.json
    {
        // ...
        require: {
            // ...
            "chewbacco/payment-qiwi-wallet-bundle": "master-dev"
        }
    }
    
.. note ::

    Please replace `master-dev` in the snippet above with the latest stable
    branch, for example ``1.0.*``.
    
Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

.. code-block :: bash

    $ php composer.phar update
    
Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your ``AppKernel.php`` file, and
register the new bundle:

.. code-block :: php

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Chewbacco\Payment\QiwiWalletBundle\ChewbaccoPaymentQiwiWalletBundle(),
        // ...
    );


Dependencies
------------
This plugin depends on the JMSPaymentCoreBundle_, so you'll need to add this to your kernel
as well even if you don't want to use its persistence capabilities.

.. code-block :: php

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new JMS\Payment\CoreBundle\JMSPaymentCoreBundle(),
        // ...
    );

Configuration
-------------

.. code-block :: yml

    // config.yml
    chewbacco_payment_qiwi_wallet:
        login: your username 
        password: your password 


.. code-block :: yml

    // routing.yml
    chewbacco_payment_qiwi_wallet:
        resource: "@ChewbaccoPaymentQiwiWalletBundle/Resources/config/routing.yml" 
        prefix:   /chewbacco/payment/qiwi_wallet

=====
Usage
=====
http://jmsyst.com/bundles/JMSPaymentCoreBundle/master/usage

.. _JMSPaymentCoreBundle: https://github.com/schmittjoh/JMSPaymentCoreBundle/blob/master/Resources/doc/index.rst
