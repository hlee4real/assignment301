<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CategoryController extends AbstractController
{
    private $em;
    public function __construct(PersistenceManagerRegistry $registry)
    {
        $this->em = $registry;
    }

    /**
     * @Route("/category", name="category_view_all")
     */
    public function categoryViewAll(){
        $category = $this->em->getRepository(Category::class)->findAll();
        return $this->render("category/index.html.twig",
        [
            'category' => $category
        ]
        );
    }

    /**
     * @Route("/category/detail/{id}", name="category_view_id")
     */
    public function categoryViewID($id){
        $category = $this->em->getRepository(Category::class)->find($id);
        if($category == null){
            $this->addFlash("Error", "Category not exist");
            return $this->redirectToRoute("category_view_all");
        }
        return $this->render("category/detail.html.twig",
        [
            'category' => $category
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/category/delete/{id}", name="category_delete")
     */
    public function categoryDelete($id){
        $category = $this->em->getRepository(Category::class)->find($id);
        if($category == null){
            $this->addFlash("Error", "There is not category that have this id to delete!");
        }else{
            $manager = $this->em->getManager();
            $manager->remove($category);
            $manager->flush();
            $this->addFlash("Success", "Category Deleted successfully");
        }
        return $this->redirectToRoute("category_view_all");
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/category/add", name="category_add")
     */
    public function categoryAdd(Request $request){
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->em->getManager();
            $manager->persist($category);
            $manager->flush();

            $this->addFlash("Success", "Add category succeed");
            return $this->redirectToRoute("category_view_all");
        }

        return $this->renderForm("category/add.html.twig",
        [
            'categoryForm' => $form
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/category/edit/{id}", name="category_edit")
     */
    public function categoryEdit(Request $request, $id){
        $category =  $this->em->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->em->getManager();
            $manager->persist($category);
            $manager->flush();

            $this->addFlash("Success", "Edit category succeed");
            return $this->redirectToRoute("category_view_all");
        }

        return $this->renderForm("category/edit.html.twig",
        [
            'categoryForm' => $form
        ]);
    }
}
