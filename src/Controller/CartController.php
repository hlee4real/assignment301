<?php

namespace App\Controller;

use App\Entity\Bill;
use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class CartController extends AbstractController
{
    private $em;
    public function __construct(PersistenceManagerRegistry $registry)
    {
        $this->em = $registry;
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function cartAdd(AuthenticationUtils $authenticationUtils, $id) :Response{
        $cart = new Cart();
        $product = $this->em->getRepository(Product::class)->find($id);
        $product_name = $product->getName();
        $product_price = $product->getPrice();
        $email = $authenticationUtils->getLastUsername();
        $quantity = 1;
        $image = $product->getImage();
        
        $cart->setQuantity($quantity);
        $cart->setProductName($product_name);
        $cart->setPrice($product_price);
        $cart->setEmail($email);
        $cart->setImage($image);
        

        $manager = $this->em->getManager();
        $manager->persist($cart);
        $manager->flush();

        return $this->redirectToRoute("products_view_all");
    }

    /**
     * @Route("/cart", name="view_cart")
     */
    public function viewCart(AuthenticationUtils $authenticationUtils): Response{
        $username = $authenticationUtils->getLastUsername();
        $cart = $this->em->getRepository(Cart::class)->findAll();
        return $this->render("cart/index.html.twig",
        [
            'cart' => $cart
        ]
    );
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete")
     */
    public function deleteCart($id){
        $cart = $this->em->getRepository(Cart::class)->find($id);
        $manager = $this->em->getManager();
        $manager->remove($cart);
        $manager->flush();
        $this->addFlash("Success", "Cart Deleted successfully");
        return $this->redirectToRoute("view_cart");
    }

    /**
     * @Route("/cart/addtobill/{id}", name="add_to_bill")
     */
    public function addToBill(AuthenticationUtils $authenticationUtils, $id){
        $username = $authenticationUtils->getLastUsername();
        $cart = $this->em->getRepository(Cart::class)->find($id);
        $manager = $this->em->getManager();
        $price = $cart->getPrice();
        $bill = $this->em->getRepository(Bill::class)->find(6);
        $currentTotal = $bill->getTotal();
        $bill->setTotal($price + $currentTotal);
        $bill->setEmail($username);
        $bill->setStatus('Pending');
        $bill->setDate(\DateTime::createFromFormat('d-m-Y', '29-12-2021'));

        $manager->persist($bill);
        $manager->remove($cart);
        $manager->flush();
        $this->addFlash("success", "add to bill successfully");
        return $this->redirectToRoute("view_cart");
    }
    
}
