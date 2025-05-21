<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\AddressRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order/billingaddress', name: 'app_order_billingaddress')]
    public function billingAddress(): Response
    {
        $user = $this->getUser();

        return $this->render('order/billingaddress.html.twig', [
            'addresses' => $user->getAddresses(),
        ]);
    }



    #[Route('/order/shippingaddress/{id}', name: 'app_order_shippingaddress')]
    public function shippingAddress(Address $billingAddress): Response
    {
        $user = $this->getUser();
        $shippingAddresses = $user->getAddresses();

        return $this->render('order/shippingaddress.html.twig', [
            'billingAddress' => $billingAddress,
            'shippingAddresses' => $shippingAddresses,
        ]);
    }









    #[Route('/order/payment/{idBilling}/{idShipping}', name: 'app_order_payment')]
    public function payment(CartService $cartService, AddressRepository $addressRepository, $idBilling, $idShipping ): Response
    {
        $billing= $addressRepository->find($idBilling);
        $shipping = $addressRepository->find($idShipping);

        return $this->render('order/payment.html.twig', [
            'cart' => $cartService->getCart(),
            'billing'=> $billing,
            'shipping'=> $shipping,
            'total'=>$cartService->getTotal(),
        ]);
    }







    #[Route('/order/validate/{idBilling}/{idShipping}', name: 'app_order_validate')]
    public function validate(CartService $cartService, AddressRepository $addressRepository, $idBilling, $idShipping, EntityManagerInterface $manager ): Response
    {
        $billing = $addressRepository->find($idBilling);
        $shipping = $addressRepository->find($idShipping);

        $order = new Order();
        $order->setBillingAddress($billing);
        $order->setShippingAddress($shipping);
        $order->setCustomer($this->getUser());
        $order->setTotal($cartService->getTotal());
        $manager->persist($order);


        foreach ($cartService->getCart() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setProduct($cartItem["product"]);
            $orderItem->setQuantity($cartItem["quantity"]);
            $orderItem->setOfOrder($order);
            $manager->persist($orderItem);
        }
        $manager->flush();
        $cartService->emptyCart();
        return $this->redirectToRoute('app_my_orders');
    }






    #[Route('/myorders', name: 'app_my_orders')]
    public function myOrders(): Response
    {
        return $this->render('order/myorders.html.twig');
    }


}

