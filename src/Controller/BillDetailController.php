<?php

namespace App\Controller;

use App\Entity\BillDetail;
use App\Form\BillDetailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BillDetailController extends AbstractController
{
    private $em;
    public function __construct(PersistenceManagerRegistry $registry)
    {
        $this->em = $registry;
    }

    /**
     * @Route("/billdetail", name="view_bill_detail")
     */
    public function viewBillDetail(){
        $billDetail = $this->em->getRepository(BillDetail::class)->findAll();
        return $this->render("bill/index.html.twig", [
            'billDetail' => $billDetail
        ]
        );
    }
    /**
     * @Route("/information", name="information")
     */
    public function information(Request $request){
        $billdetail = new BillDetail();
        $form = $this->createForm(BillDetailType::class, $billdetail);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->em->getManager();
            $manager->persist($billdetail);
            $manager->flush();

            $this->addFlash("Success", "Make order succeed");
            return $this->redirectToRoute("products_view_all");
        }
        return $this->renderForm("bill_detail/makeinfo.html.twig",
        [
            'billDetailForm' => $form
        ]);
    }
}
