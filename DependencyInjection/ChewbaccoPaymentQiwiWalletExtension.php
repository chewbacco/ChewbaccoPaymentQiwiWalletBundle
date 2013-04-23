<?php

namespace Chewbacco\Payment\QiwiWalletBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ChewbaccoPaymentQiwiWalletExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!isset($config['login'])) {
            throw new \InvalidArgumentException('The "chewbaccо_payment_qiwi_wallet.login" option must be set');
        }
        if (!isset($config['password'])) {
            throw new \InvalidArgumentException('The "chewbaccо_payment_qiwi_wallet.password" option must be set');
        }

        $container->setParameter('chewbaccо_payment_qiwi_wallet.login', $config['login']);
        $container->setParameter('chewbaccо_payment_qiwi_wallet.password', $config['password']);
        $container->setParameter('chewbaccо_payment_qiwi_wallet.return_url', $config['return_url']);

    }
}
