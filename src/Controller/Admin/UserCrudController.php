<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository; 
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @method User getUser()
 */
class UserCrudController extends AbstractCrudController
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $userId = $this->getUser()->getId();
        
        // Créez le QueryBuilder avec l'alias 'u'
        $qb = $this->userRepository->createQueryBuilder('u'); // Utilisez un alias de type string
    
        // Corrigez la syntaxe ici
        $qb->andWhere('u.id != :userId') // Utilisez '!=' pour vérifier que l'identifiant de l'utilisateur n'est pas égal à l'identifiant courant
           ->setParameter('userId', $userId);
        
        // Vous pouvez également ajouter d'autres filtres ou recherches ici si nécessaire.
    
        return $qb;
    }
    

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('username')
            ->setLabel('Nom d\'utilisateur')
            ->setRequired(true);

        yield TextField::new('password')
            ->setLabel('Mot de passe')
            ->onlyOnForms()
            ->setRequired(true)
            ->setFormTypeOption('attr', ['type' => 'password']);

        yield ChoiceField::new('roles')
            ->setLabel('Rôles')
            ->allowMultipleChoices()
            ->renderAsBadges([
                'ROLE_ADMIN' => 'success',
                'ROLE_AUTHOR' => 'warning',
            ])
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Auteur' => 'ROLE_AUTHOR',
            ]);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var User $user */
        $user = $entityInstance;

        if ($user->getPassword()) {
            $plainPassword = $user->getPassword();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        parent::persistEntity($entityManager, $user);
    }
}
