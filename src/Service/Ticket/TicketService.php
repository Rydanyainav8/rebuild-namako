<?php

namespace App\Service\Ticket;

use App\Entity\Ticket;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\TicketRepository;
use Doctrine\Persistence\ManagerRegistry;

class TicketService
{
    protected $session;

    protected $ticketRepository;

    public function __construct(SessionInterface $session, TicketRepository $ticketRepo)
    {
        $this->session = $session;
        $this->ticketRepository = $ticketRepo;

    }
    public function add(int $numero)
    {
        $panier = $this->session->get('panier', []);


        if (!empty($panier[$numero])) {
            $panier[$numero]++;
        } else {
            $panier[$numero] = 1;
        }
        $this->session->set('panier', $panier);
    }

    public function remove(int $numero)
	{
		$panier = $this->session->get('panier', []);

        if(!empty($panier[$numero])) {
            unset($panier[$numero]);
        }

        $this->session->set('panier', $panier);
	}

    public function getFullCart()
	{
		$panier = $this->session->get('panier', []);
        $dataTicket = [];
        
        foreach ($panier as $numero => $quantity) {
            $dataTicket[] = [
                'ticket' => $this->ticketRepository->FindNumero($numero),
                'quantity' => $quantity
            ];
        }
        // dd($dataTicket);
        return $dataTicket;
	}

	public function getTotal() : float
	{
		$total = 0;

        foreach ($this->getFullCart() as $item) {
            $total += $item['ticket']->getValue() * $item['quantity'];
        }
        return $total;
	}
}