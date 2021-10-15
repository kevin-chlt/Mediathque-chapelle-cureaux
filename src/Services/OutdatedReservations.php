<?php

namespace App\Services;

class OutdatedReservations
{
    #Select reservation outdated since 3 weeks, used for user panel.
    public function getOutdatedReservation (array $reservations) : array
    {
        $outdatedReservations = [];

        foreach ($reservations as $reservation) {
            $reservedAt = new \DateTime($reservation->getReservedAt()->format('Y-m-d H:m:s'));
            $date_now = new \DateTime();
            if($reservation->getIsCollected() && ($reservedAt->add(new \DateInterval('P3W')) < $date_now) ) {
                $outdatedReservations[] = $reservation;
            }
        }
        return $outdatedReservations;
    }

    #Select reservation outdated since 3day, used for admin panel
    public function getOutdatedReservationAdminPanel (array $reservations) : array
    {
        $outdatedReservations = [];
        foreach($reservations as $reservation) {
            $reservedAt  = new \DateTime($reservation->getReservedAt()->format('Y-m-d H:m:s'));
            $date_now = new \DateTime();
            if(!$reservation->getIsCollected() && ($reservedAt->add(new \DateInterval('P3D')) < $date_now)){
                $outdatedReservations[] = $reservation;
            }
        }
        return $outdatedReservations;
    }

}