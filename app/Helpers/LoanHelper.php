<?php

namespace App\Helpers;

use App\Exceptions\InvalidInputException;

class LoanHelper
{
    public function calculateRepayment(float $amount, int $term, $requestDate): array
    {
        $result = [];
        if($amount <= 0 || $term <= 0){
            throw new InvalidInputException("Amount and value must be larger than zero");
        }
        $avgAmount = round($amount / $term, 2);
        $date = \DateTime::createFromFormat('Y-m-d', $requestDate);
        if(!$date){
            throw new InvalidInputException("Date format should be Y-m-d");
        }
        $dateInterval = \DateInterval::createFromDateString('7 day');
        $date = $date->add($dateInterval);
        for ($i = 0; $i < $term; $i++) {
            $item = ['pay_date' => $date->format('Y-m-d')];
            if ($i == $term - 1) {
                $item['amount'] = round($amount - $avgAmount * ($term - 1), 2);
            } else {
                $item['amount'] = $avgAmount;
            }
            $result[] = $item;
            $date = $date->add($dateInterval);
        }
        return $result;
    }
}
