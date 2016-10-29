<?php

namespace Lincode\Fly\Bundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserProfilePermissionRepository extends EntityRepository
{
    public function removeFromProfile($profile)
    {
        $em = $this->getEntityManager();
        $tableName = $em->getClassMetadata('FlyBundle:UserProfilePermission')->getTableName();

        $sql =  "DELETE FROM " . $tableName . " WHERE profileId = :profileId";
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue("profileId", $profile->getId());
        $statement->execute();
    }

    public function listRoutes($profileId)
    {
        if (empty($profileId)) {
            return array();
        }

        $em = $this->getEntityManager();
        $tableName = $em->getClassMetadata('FlyBundle:UserProfilePermission')->getTableName();

        $sql =  "SELECT route, params FROM " . $tableName . " WHERE profileId = :profileId";
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue("profileId", $profileId);
        $statement->execute();

        return $statement->fetchAll();
    }
}
