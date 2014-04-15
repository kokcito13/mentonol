<?php

namespace Acme\DemoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;
use Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints\Count;

class SiteType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('keyManagers',
                  'entity', array(
                                 'class' => 'ApplicationSonataUserBundle:User',
                                 'query_builder' => function(EntityRepository $er) {
                                     return $er->createQueryBuilder('u')
                                         ->where('u.roles LIKE \'%ROLE_KEY_MANAGER%\'')
                                         ->orderBy('u.username', 'ASC')
                                         ;
                                 },
                                 'multiple' => true,
                                 'constraints' => array(
                                     new Count(array(
                                                    'min' => 1,
                                                    'minMessage' => 'Выберите минимум одного Кей-менеджера',
                                               )),
                                 )
                            ))
            ->add('editors',
                'entity', array(
                        'class' => 'ApplicationSonataUserBundle:User',
                        'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                ->where('u.roles LIKE \'%ROLE_EDITOR%\'')
                                ->orderBy('u.username', 'ASC')
                                ;
                        },
                        'multiple' => true,
                        'constraints' => array(
                            new Count(array(
                                           'min' => 1,
                                           'minMessage' => 'Выберите минимум одного редактора',
                                      )),
                        )
                ))
            ->add('submit', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\DemoBundle\Entity\Site'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acme_demobundle_site';
    }
}
