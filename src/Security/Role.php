<?php

declare(strict_types=1);

namespace App\Security;

class Role
{
    public const ROLES = [
        'ROLE_ADMIN' => 'Admin',
        'ROLE_USER_EDIT' => 'Utilisateurs (modifier)',
        'ROLE_PARAMETRAGE' => 'Paramétrage',

        'ROLE_STRUCTURE_VIEW' => 'Structure (voir)',
        'ROLE_STRUCTURE_EDIT' => 'Structure (modifier)',
        'ROLE_STRUCTURE_EDIT_CONDITION' => 'Structure (modifier si même service)',
        'ROLE_STRUCTURE_CREATE' => 'Structure (créer)',
        'ROLE_STRUCTURE_DELETE' => 'Structure (supprimer)',

        'ROLE_PERSONNE_VIEW' => 'Personne (voir)',
        'ROLE_PERSONNE_EDIT' => 'Personne (modifier)',
        'ROLE_PERSONNE_EDIT_CONDITION' => 'Personne (modifier si même service)',
        'ROLE_PERSONNE_CREATE' => 'Personne (créer)',
        'ROLE_PERSONNE_DELETE' => 'Personne (supprimer)',

        'ROLE_VIP_EDIT' => 'Personne VIP (voir et modifier)',

        'ROLE_CONTACT_VIEW' => 'Contact (voir)',
        'ROLE_CONTACT_CREATE' => 'Contact (créer)',
        'ROLE_CONTACT_EDIT' => 'Contact (administrer)',
        'ROLE_CONTACT_DELETE' => 'Contact (supprimer)',
    ];
}
