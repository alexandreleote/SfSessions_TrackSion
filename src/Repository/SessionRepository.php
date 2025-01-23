<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\Stagiaire;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function findByCurrentSessions(): array
    {

        return $this->createQueryBuilder('s')
            ->where('s.dateDebut <= :now AND s.dateFin >= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('s.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByNextSessions(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.dateDebut > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('s.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByPastSessions(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.dateFin < :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('s.dateFin', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findNonInscrits($session_id)
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;

        $qb->select('s')
           ->from('App\Entity\Stagiaire','s')
           ->leftJoin('s.sessions','se')
           ->where('se.id = :id');
        
        $sub = $em->createQueryBuilder();

        $sub->select('st')
            ->from('App\Entity\Stagiaire','st')
            ->where($sub->expr()->notIn('st.id', $qb->getDQL()))
            ->setParameter('id', $session_id)
            ->orderBy('st.nom', 'ASC')
            ->addOrderBy('st.prenom', 'ASC');
        
        $query = $sub->getQuery();
        return $query->getResult();
    }

    public function findNonProgrammes($session_id)
    {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;

        $qb->select('c')
           ->from('App\Entity\Cours', 'c')
           ->leftJoin('c.programmes', 'p')
           ->leftJoin('p.session', 'se')
           ->where('se.id = :id');
        
        $sub = $em->createQueryBuilder();

        $sub->select('co')
            ->from('App\Entity\Cours', 'co')
            ->where($sub->expr()->notIn('co.id', $qb->getDQL()))
            ->setParameter('id', $session_id)
            ->orderBy('co.intitule');
        
        $query = $sub->getQuery();
        return $query->getResult();
    }

    public function findCurrentSessionsByStudent(Stagiaire $stagiaire): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.dateDebut <= :now')
            ->andWhere('s.dateFin >= :now')
            ->andWhere(':stagiaire MEMBER OF s.stagiaires')
            ->setParameter('now', new \DateTime())
            ->setParameter('stagiaire', $stagiaire)
            ->orderBy('s.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findNextSessionsByStudent(Stagiaire $stagiaire): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.dateDebut > :now')
            ->andWhere(':stagiaire MEMBER OF s.stagiaires')
            ->setParameter('now', new \DateTime())
            ->setParameter('stagiaire', $stagiaire)
            ->orderBy('s.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPastSessionsByStudent(Stagiaire $stagiaire): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.dateFin < :now')
            ->andWhere(':stagiaire MEMBER OF s.stagiaires')
            ->setParameter('now', new \DateTime())
            ->setParameter('stagiaire', $stagiaire)
            ->orderBy('s.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
