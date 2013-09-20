<?php
/**
 * Created by : Vincent SAISSET
 * Date: 05/09/13
 * Time: 14:56
 */

namespace ICAP\DropZoneBundle\Repository;


use Doctrine\ORM\EntityRepository;

class DropRepository extends EntityRepository {

    public function drawDropForCorrection($dropZone, $user)
    {
        // TODO verifier le nombre de correction valide (avec date pas trop vieille) sur les drops piochables

        $possibleIds = $this
            ->createQueryBuilder('drop')
            ->select('drop.id')
            ->andWhere('drop.dropZone = :dropZone')
            ->andWhere('drop.user != :user')
            ->setParameter('dropZone', $dropZone)
            ->setParameter('user', $user)
            ->getQuery()->getResult();

        if (count($possibleIds) == 0) {
            return null;
        }

        $randomIndex = rand(0, (count($possibleIds)-1));
        $dropId = $possibleIds[$randomIndex];

        return $this->find($dropId);
    }
}