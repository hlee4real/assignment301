<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use function PHPUnit\Framework\throwException;

class ProductController extends AbstractController
{
    private $em;

    public function __construct(PersistenceManagerRegistry $registry)
    {
        $this->em = $registry;
    }
    
    /**
     * @Route("/products", name="products_view_all")
     */
    public function productViewAll()
    {
        $products = $this->em->getRepository(Product::class)->findAll();
        return $this->render("product/index.html.twig",
        [
            'products' => $products
        ]
    );
    }

    /**
     * @Route("/products/detail/{id}", name="products_view_by_id")
     */
    public function productViewId($id){
        $productsid = $this->em->getRepository(Product::class)->find($id);
        if($productsid == null){
            $this->addFlash("Error", "Product not exist");
            return $this->redirectToRoute("product_view_all");
        }
        return $this->render("product/detail.html.twig",
        [
            'productsid' => $productsid
        ]
    );
    }
    
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/products/delete/{id}", name="products_delete")
     */
    public function productDelete($id){
        $productdelete = $this->em->getRepository(Product::class)->find($id);
        if($productdelete == null){
            $this->addFlash("Error", "There is not product that have this id to delete!");
        }else{
            $manager = $this->em->getManager();
            $manager->remove($productdelete);
            $manager->flush();
            $this->addFlash("Success", "Product Deleted successfully");
        }
        return $this->redirectToRoute("products_view_all");
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/product/add", name="product_add")
     */
    public function productAdd(Request $request){
        $productadd = new Product();
        $form = $this->createForm(ProductType::class, $productadd);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $image = $productadd->getImage();
            $imgName = uniqid();
            $imgExtension = $image->guessExtension();
            $imageName = $imgName . "." . $imgExtension;
            try{
                $image->move(
                    $this->getParameter('image'), $imageName
                );
            }catch(FileException $e){
                throwException($e);
            }
            $productadd->setImage($imageName);
            $manager = $this->em->getManager();
            $manager->persist($productadd);
            $manager->flush();
            $this->addFlash("Success", "Add product Success");
            return $this->redirectToRoute("products_view_all");
        }
        return $this->renderForm("product/add.html.twig",
        [
            'productForm' => $form
        ]
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/product/edit/{id}", name="products_edit")
     */
    public function productEdit(Request $request, $id){
        $productedit = $this->em->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class, $productedit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $form['image']->getData();
            if($file != null){
                $image = $productedit->getImage();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . "." . $imgExtension;
                try{
                    $image->move($this->getParameter('image'), $imageName);
                }catch(FileException $e){
                    throwException($e);
                }
                $productedit->setImage($imageName);
            }
            $manager = $this->em->getManager();
            $manager->persist($productedit);
            $manager->flush();
            $this->addFlash("Success", "Edit Product Success");
            return $this->redirectToRoute("products_view_all");
        }
        return $this->renderForm("product/edit.html.twig",
        [
            'productForm' => $form
        ]
        );
    }
}
