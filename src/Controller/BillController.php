<?php

namespace App\Controller;

use App\Entity\Bill;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
class BillController extends AbstractController
{
    private $em;
    public function __construct(PersistenceManagerRegistry $registry)
    {
        $this->em = $registry;
    }

    /**
     * @Route("/bill", name="view_bill")
     */
    public function viewBill(){
        $bill = $this->em->getRepository(Bill::class)->findAll();
        return $this->render("bill/index.html.twig", [
            'bill' => $bill
        ]
        );
    }

    /**
     * @Route("/bill/delete/{id}", name="delete_bill")
     */
    public function deleteBill($id){
        $bill = $this->em->getRepository(Bill::class)->find($id);
        $manager = $this->em->getManager();
        $manager->remove($bill);
        $manager->flush();
        $this->addFlash("Success", "Bill Deleted successfully");
        return $this->redirectToRoute("view_bill");
    }


}
