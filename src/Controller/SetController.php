<?php

namespace App\Controller;

use App\Entity\Set;
use App\Form\SetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SetController extends AbstractController
{
    private $em;
    public function __construct(PersistenceManagerRegistry $registry)
    {
        $this->em = $registry;
    }

    /**
     * @Route("/set", name="set_view_all")
     */
    public function setViewAll(){
        $set = $this->em->getRepository(Set::class)->findAll();
        return $this->render("set/index.html.twig",
        [
            'set' => $set
        ]
        );
    }

    /**
     * @Route("/set/detail/{id}", name="set_view_id")
     */
    public function setViewID($id){
        $set = $this->em->getRepository(Set::class)->find($id);
        if($set == null){
            $this->addFlash("Error", "Set not exist");
            return $this->redirectToRoute("set_view_all");
        }
        return $this->render("set/detail.html.twig",
        [
            'set' => $set
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/category/delete/{id}", name="set_delete")
     */
    public function setDelete($id){
        $set = $this->em->getRepository(Set::class)->find($id);
        if($set == null){
            $this->addFlash("Error", "There is not set that have this id to delete!");
        }else{
            $manager = $this->em->getManager();
            $manager->remove($set);
            $manager->flush();
            $this->addFlash("Success", "Set Deleted successfully");
        }
        return $this->redirectToRoute("set_view_all");
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/set/add", name="set_add")
     */
    public function setAdd(Request $request){
        $set = new Set();
        $form = $this->createForm(SetType::class, $set);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->em->getManager();
            $manager->persist($set);
            $manager->flush();

            $this->addFlash("Success", "Add set succeed");
            return $this->redirectToRoute("set_view_all");
        }

        return $this->renderForm("set/add.html.twig",
        [
            'setForm' => $form
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("set/edit/{id}", name="set_edit")
     */
    public function setEdit(Request $request, $id){
        $set =  $this->em->getRepository(Set::class)->find($id);
        $form = $this->createForm(SetType::class, $set);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->em->getManager();
            $manager->persist($set);
            $manager->flush();

            $this->addFlash("Success", "Edit set succeed");
            return $this->redirectToRoute("set_view_all");
        }

        return $this->renderForm("set/edit.html.twig",
        [
            'setForm' => $form
        ]);
    }
}
