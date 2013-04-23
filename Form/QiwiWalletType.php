<?php
namespace Chewbacco\Payment\QiwiWalletBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class QiwiWalletType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('number', 'text', array('label' => 'form.data_qiwi_wallet.label.number', 'required' => false));
    }

    public function getName()
    {
        return 'qiwi_wallet';
    }
}
